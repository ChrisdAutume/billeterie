@extends('layouts.dashboard')

@section('title')
    Commande
@endsection

@section('smalltitle')
    Récapitulatif
@endsection

@section('content')
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

    <div class="box">
        <div class="box-header">
            <h3 class="box-title">Commandes</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <table id="commandes" class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>id</th>
                    <th>Acheteur</th>
                    <th>Places</th>
                    <th>Don</th>
                    <th>Statut</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach($orders as $order)
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->name }} {{ $order->surname }}</td>
                        <td><ul>
                                @foreach($order->billets as $billet)
                                    <li>{{ $billet->name }} {{ $billet->surname }} ({{ $billet->price->name }}) <a
                                                class="btn btn-success btn-xs" href="{{ route('admin_billet_mail', ['id'=>$billet->id]) }}"><i class="fa fa-envelope"></i></a>
                                        <a
                                                class="btn btn-info btn-xs" href="{{ route('admin_billet_view', ['billet'=>$billet]) }}"><i class="fa fa-eye"></i></a>
                                        @if(is_null($billet->validated_at))<a
                                                class="btn btn-info btn-xs validate_link" data-href="{{ $billet->id }}"><i class="fa fa-ticket"></i> Valider l'entrée</a>
                                    @endif </li>
                                @endforeach
                            </ul></td>
                        <td>@if($order->dons->count() > 0) {{ $order->dons[0]->amount/100 }} € @else <i>Aucun</i> @endif</td>
                        <td>{{ $order->state }}</td>
                        <td><a class="btn btn-xs btn-flat btn-warning" href="{{ route('admin_order_edit', ['order'=>$order]) }}"><i class="fa fa-pencil"></i></a></td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr>
                    <th>id</th>
                    <th>Acheteur</th>
                    <th>Places</th>
                    <th>Don</th>
                    <th>Statut</th>
                    <th>Action</th>
                </tr>
                </tfoot>
            </table>
        </div>
        <!-- /.box-body -->
    </div>
    <!-- Reset button -->
    <div class="box box-danger collapsed-box">

        <div class="box-header with-border">
            <h3 class="box-title">Zone danger !</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
            </div>
        </div>
        <div class="box-body">
            <a class="btn btn-danger" href="{{ url()->route('admin_reset_all') }}" role="button">Suppression complet du site !</a>
        </div>
    </div>

@endsection
@section('js')
    @parent
    <script src="{{ @asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ @asset('plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
@endsection
@section('sublayout-js')
    <script>
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.11.1/typeahead.bundle.min.js"></script>
    <script type="application/javascript">
        jQuery(document).ready(function($) {

            //Show notifications
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
            };
            $('#search').change(function(event){
                event.preventDefault();
                $.get("{{ route('guichet_validate') }}", {code: $(this).val()})
                    .done(function(data){
                        responseCheck(data);
                    });
            });
            $('#search').focus();

            $("#commandes").DataTable();

            $('#commandes').on('draw.dt', function () {
                setTimeout(function () {
                    console.log('page change', $('.validate_link'));
                    $('.validate_link').off('click').on('click', function() {
                        console.log('CLIKED');
                        $.get("{{ route('guichet_validate') }}",{id:$(this).attr('data-href')})
                            .done(function(data){
                                console.log('got res');
                                responseCheck(data);
                            });
                    });
                }, 50);
            });
        });
    </script>

@endsection

