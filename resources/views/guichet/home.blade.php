@extends('layouts.dashboard')

@section('title')
    Guichet
@endsection

@section('smalltitle')
    Controle des billets
@endsection

@section('content')

    <div class="alert alert-success" id="billet_ok" style="display: none">
        <h4><i class="icon fa fa-check"></i> Billet validé !</h4>
        Détenteur <strong id="identity"></strong>
    </div>
    <div class="alert alert-danger" id="billet_wrong" style="display: none">
        <h4><i class="icon fa fa-ban"></i> ATTENTION !</h4>
        Le billet de <strong id="identity_error"></strong> a déja été validé <strong id="datetime"></strong>.
    </div>
    <div class="alert alert-warning" id="billet_inconnu" style="display: none">
        <h4><i class="icon fa fa-ban"></i> INCONNU !</h4>
        Le code n'est pas reconnu.
    </div>
    <div class="box box-default">

        <div class="box-header with-border">
            <h3 class="box-title">Recherche</h3>
        </div>
        <div class="box-body">
            <form action="#" id="search_submit">
            <div class="input-group input-group-lg">
                <input type="text" class="form-control" id="search">
                <span class="input-group-btn">
                      <input type="submit" class="btn btn-info btn-flat">Go!</input>
                    </span>
            </div>
            </form>
        </div>
    </div>

@endsection

@section('sublayout-js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.11.1/typeahead.bundle.min.js"></script>
    <script type="application/javascript">
            var path = "{{ route('guichet_autocomplete') }}";
            function isNumeric(n) {
                var regex = /\d/g;
                return regex.test(n);
            }

            jQuery(document).ready(function($) {

                //Show notifications
                var billetValidated = function(obj){
                    $('#billet_ok').hide(100);
                    $('#billet_wrong').hide(100);
                    $('#billet_inconnu').hide(100);

                    $("#identity").html(obj.name + ' '+ obj.surname);
                    $('#billet_ok').show(100);
                };

                var billetError = function(obj){
                    $('#billet_ok').hide(100);
                    $('#billet_wrong').hide(100);
                    $('#billet_inconnu').hide(100);

                    $("#identity_error").html(obj.name + ' '+ obj.surname);
                    $("#datetime").html(obj.validated_at);

                    $('#billet_wrong').show(100);
                };

                var billetInconnu = function(){
                    $('#billet_ok').hide(100);
                    $('#billet_wrong').hide(100);
                    $('#billet_inconnu').hide(100);

                    $('#billet_inconnu').show(100);
                };

                //Check request
                var responseCheck = function (obj) {
                    switch (obj.validated) {
                        case true:
                            billetValidated(obj);
                            break;
                        case 'already':
                            billetError(obj);
                            break;
                        case false:
                        default:
                            billetInconnu();
                    }
                    Typeahead.typeahead('val','');
                    $('#search').focus();
                };

                var engine = new Bloodhound({
                    remote: {
                        url: path+'?q=%QUERY%',
                        wildcard: '%QUERY%'
                    },
                    datumTokenizer: Bloodhound.tokenizers.whitespace('q'),
                    queryTokenizer: Bloodhound.tokenizers.whitespace
                });

                var Typeahead = $("#search").typeahead({
                    hint: false,
                    highlight: true,
                    minLength: 3
                }, {
                    source: engine.ttAdapter(),
                    name: 'billetList',

                    templates: {
                        empty: [
                            '<div class="list-group search-results-dropdown"><div class="list-group-item">Aucune correspondance</div></div>'
                        ],
                        header: [
                            '<div class="list-group search-results-dropdown">'
                        ],
                        suggestion: function (data) {
                            return '<a class="list-group-item">' + data.name + ' '+ data.surname +'</a>'
                        }
                    }
                });
                $('#search').bind('typeahead:selected', function(obj, datum, name) {
                    $.get("{{ route('guichet_validate') }}", {id: datum.id})
                            .done(function(data){
                        console.log(data);
                        responseCheck(data);
                    });
                });
                $('#search').change(function(event){
                    event.preventDefault();
                    $.get("{{ route('guichet_validate') }}", {code: $(this).val()})
                            .done(function(data){
                                responseCheck(data);
                            });
                });
                $('#search').focus();
            });
    </script>
@endsection

@section('old')
    source:  function (query, process) {
    if(isNumeric(query))
    return false;

    if(query.length < 3)
    return false;
    return $.get(path, { query: query }, function (data) {
    return process(data);
    });
    },

    engine.ttAdapter()
    @endsection