@extends('layouts.dashboard')

@section('title')
    Guichet
@endsection

@section('smalltitle')
    Controle des billets
@endsection

@section('content')
    <div class="alert alert-danger" id="offline_mode" style="display: none">
        <h4><i class="icon fa fa-warning"></i>CONNEXION INDISPONIBLE !</h4>
        La validation est toujours possible en cliquant sur le nom de la personne, mais <strong>prévenez votre responsable</strong> afin que le problème soit réglé le plus rapidement possible !
    </div>
    <div class="alert alert-success" id="billet_ok" style="display: none">
        <h4><i class="icon fa fa-check"></i> Billet validé !</h4>
        Détenteur <strong id="identity"></strong>
        <br>Options <strong id="options"></strong>
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
            <form action="#">
            <div class="input-group">
                <input type="text" class="form-control" id="search">
                <span class="input-group-btn input-group-append">
                    <input type="submit" class="btn btn-info" id="search_button" value="Go!"/>
                </span>
            </div>
            </form>
        </div>
    </div>

@endsection
@section('css')
    <style>
        div.tt-menu {
            top: 32px !important;
        }

        div.search-results-dropdown {
            margin-bottom: 0;
        }

        span.twitter-typeahead {
            display: block !important;
        }

        span.twitter-typeahead .tt-dropdown-menu {
            position: absolute;
            top: 0;
            left: 0;
            z-index: 1000;
            display: none;
            float: left;
            min-width: 160px;
            padding: 5px 0;
            margin: 2px 0 0;
            list-style: none;
            font-size: 14px;
            text-align: left;
            background-color: #ffffff;
            border: 1px solid #cccccc;
            border: 1px solid rgba(0, 0, 0, 0.15);
            border-radius: 4px;
            -webkit-box-shadow: 0 6px 12px rgba(0, 0, 0, 0.175);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.175);
            background-clip: padding-box;
        }
        span.twitter-typeahead .tt-suggestion > p {
            display: block;
            padding: 3px 20px;
            clear: both;
            font-weight: normal;
            line-height: 1.42857143;
            color: #333333;
            white-space: nowrap;
        }
        span.twitter-typeahead .tt-suggestion > p:hover,
        span.twitter-typeahead .tt-suggestion > p:focus {
            color: #ffffff;
            text-decoration: none;
            outline: 0;
            background-color: #428bca;
        }
        span.twitter-typeahead .tt-suggestion.tt-cursor {
            color: #ffffff;
            background-color: #428bca;
        }
        span.twitter-typeahead {
            width: 100%;
        }
        .input-group span.twitter-typeahead {
            display: block !important;
        }
    </style>
@endsection
@section('sublayout-js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.11.1/typeahead.bundle.min.js"></script>
    <script type="application/javascript">

        var offline = false;

        /**
         * Data pulled from server every 10 sec for autocompletion and offline mode
         */
        var offlineData = {
            billets: [],
        };

        /**
         * List of billet validated offline
         * {id => datetime, id => datetime, ...}
         */
        var offlineValidated = {};

        // Try to reload offline data from localstorage
        try {
            var tmpData = JSON.parse(window.localStorage.getItem('guichet_offline_data'));
            tmpData.serverTime = new Date(tmpData.serverTime);
            tmpData.clientTime = new Date(tmpData.clientTime);
            var tmpValidated = JSON.parse(window.localStorage.getItem('guichet_offline_validated'));
            // If there is no error we assign all vars
            offlineData = tmpData;
            offlineValidated = tmpValidated;
        } catch (e) {}


        jQuery(document).ready(function($) {


            /*
             * Style functions
             */
            var billetValidated = function(obj){
                $('#billet_ok').hide(100);
                $('#billet_wrong').hide(100);
                $('#billet_inconnu').hide(100);

                $("#identity").html(obj.name + ' '+ obj.surname);
                $("#options").html(obj.options);
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

            var setOffline = function(offlineMode){
                offline = !!offlineMode;
                if(offline) {
                    $('#offline_mode').show(100);
                    $('#search_button').attr('disabled', true);
                }
                else {
                    $('#offline_mode').hide(100);
                    $('#search_button').attr('disabled', false);
                }
            };


            //Check response
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

            /**
             * Offline validation fallback
             * @param billet Billet object given by autocompletion, should be a reference to an object from offlineData
             */
            var offlineValidate = function (billet) {
                if (billet.validated_at) {
                    billetError(billet);
                }
                else {
                    billetValidated(billet);
                    var datetime = new Date(Date.now() + (offlineData.serverTime - offlineData.clientTime));
                    billet.validated_at = datetime;
                    offlineValidated[billet.id] = datetime;
                    window.localStorage.setItem('guichet_offline_validated', JSON.stringify(offlineValidated));
                    window.localStorage.setItem('guichet_offline_data', JSON.stringify(offlineData));
                }
            };

            /**
             * Matcher used by Typeahead to search in our offline data
             */
            var substringMatcher = function(query, callback) {
                var matches = [];
                query = (query+'').toLowerCase();

                $.each(offlineData.billets, function(i, billet) {
                    if ((billet.name + ' ' + billet.surname + ' (' + billet.mail + ')').toLowerCase().indexOf(query) !== -1 || query.substr(0, query.length-offlineData.reducedCodeLength) == (billet.code+'').toLowerCase()) {
                        matches.push(billet);
                    }
                });

                callback(matches);
            };

            /**
             * autocompletion configuration
             */
            var Typeahead = $("#search").typeahead({
                hint: true,
                highlight: true,
                minLength: 3
            }, {
                source: substringMatcher,
                name: 'billetList',

                templates: {
                    empty: [
                        '<div class="list-group search-results-dropdown"><div class="list-group-item">Aucune correspondance</div></div>'
                    ],
                    header: [
                        '<div class="list-group search-results-dropdown">'
                    ],
                    suggestion: function (data) {
                        return '<a class="list-group-item">' + data.name + ' '+ data.surname  + ' (' + data.mail + ')</a>'
                    }
                }
            });
            $('#search').bind('typeahead:selected', function(obj, datum, name) {
                $("#search").typeahead('val', '');
                $.get("{{ route('guichet_validate') }}", {id: datum.id})
                .done(function(data){
                    setOffline(false);
                    responseCheck(data);
                })
                .fail(function() {
                    setOffline(true);
                    offlineValidate(datum);
                });
            });
            $('#search').change(function(event){
                event.preventDefault();
                $.get("{{ route('guichet_validate') }}", {code: $(this).val()})
                    .done(function(data){
                        $("#search").typeahead('val', '');
                        responseCheck(data);
                    })
                    .fail(function() {
                        setOffline(true);
                    });
            });
            $('#search').focus();

            // Pull offline data every 10 seconds and a first time
            var pullOfflineData = function() {
                $.get("{{ route('guichet_offline_data') }}")
                .done(function(data){
                    // Check format and save it to localstorage
                    if(data.serverTime && data.reducedCodeLength) {
                        data.serverTime = new Date(data.serverTime);
                        data.clientTime = new Date();
                        offlineData = data;
                        window.localStorage.setItem('guichet_offline_data', JSON.stringify(data));
                        setOffline(false);

                    }
                    else {
                        console.warn('Offline data format error');
                        setOffline(true);
                    }
                })
                .fail(function() {
                    setOffline(true);
                });
            };
            pullOfflineData();
            setInterval(pullOfflineData, 10000);

            // Push offline validation every minutes if there is some
            var pushOfflineValidation = function() {
                if (offlineValidated && Object.keys(offlineValidated).length > 0) {
                    $.ajax("{{ route('guichet_offline_validation') }}", {
                        data: JSON.stringify(offlineValidated),
                        contentType: 'application/json',
                        type: 'POST',
                        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                    })
                    .done(function(){
                        offlineValidated = {};
                        window.localStorage.setItem('guichet_offline_validated', JSON.stringify(offlineValidated));
                    })
                    .fail(function() {
                        setOffline(true);
                    });
                }
            };
            pushOfflineValidation();
            setInterval(pushOfflineValidation, 5000);
        });


        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/gichet-sw.js', { scope: '/' }).then(function(reg) {
                if(reg.installing) {
                    console.log('Service worker installing');
                } else if(reg.waiting) {
                    console.log('Service worker installed');
                } else if(reg.active) {
                    console.log('Service worker active');
                }
            }).catch(function(error) {
                console.log('Error: Do not refresh the page in case of offline mode. (Registration failed with ' + error + ').');
            });
        }
        else {
            console.log('Error: Do not refresh the page in case of offline mode. (serviceWorker is not available).')
        }
    </script>
@endsection
