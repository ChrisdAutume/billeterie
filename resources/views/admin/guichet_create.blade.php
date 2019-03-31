@extends('layouts.dashboard')

@section('title')
    Guichet
@endsection

@section('smalltitle')
    gestion
@endsection

@section('content')
    <div class="callout callout-info">
        <h4><i class="fa fa-question-circle"></i> Mais qu'est ce qu'un guichet ?</h4>
        <p>Un guichet a deux objectifs: la vente de billet via des entitées extérieurs (partenaires / membres ...) ou la validation des entrées via application mobile. </p>
        <p>Un e-mail est automatiquement émis pour informer de la création du guichet. Les paramétres de configuration des appareils mobiles est disponible via le bouton <i class="fa fa-qrcode"></i></p>
    </div>
    <div class="box box-default">

        <div class="box-header with-border">
            <h3 class="box-title">Création d'un guichet</h3>
        </div>
        <div class="box-body">
            <form class="form-horizontal" action="" method="post" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="form-group">
                    <label for="liste" class="col-lg-2 text-right">Nom du guichet</label>
                    <div class="col-lg-10">
                        <input type="text" name="name" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label for="liste" class="col-lg-2 text-right">Adresse mail de liaison</label>
                    <div class="col-lg-10">
                        <input type="email" name="mail" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-lg-2 text-right">Type de guichet</label>
                    <div class="col-lg-10">
                        <select class="form-control" id="type" name="type">
                            <option value="sell">Guichet de vente</option>
                            <option value="validation">Guichet de validation & API</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="liste" class="col-lg-2 text-right">Billets autorisé (uniquement pour la vente)</label>
                    <div class="col-lg-10">
                        <select class="form-control" id="acl" multiple="multiple" name="acl[]">
                            @foreach($prices as $price)
                                <option value="{{ $price->id }}">{{ $price->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="liste" class="col-lg-2 text-right">Date de début de validité</label>
                    <div class="col-lg-10">
                        <div class='input-group date' id='start_date'>
                            <input type='text' class="form-control" name="start_at" />
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="liste" class="col-lg-2 text-right">Date de fin de validité
                    </label>
                    <div class="col-lg-10">
                        <div class='input-group date' id='end_date'>
                            <input type='text' class="form-control" name="end_at"/>
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>
                </div>

                <input type="submit" class="btn btn-success form-control" value="Ajouter !" />
            </form>
        </div>
    </div>

    <div class="box box-default">

        <div class="box-header with-border">
            <h3 class="box-title">Liste des guichets</h3>
        </div>
        <div class="box-body">
            <table class="table table-bordered table-hover">
                <tbody><tr>
                    <th style="width: 10px">#</th>
                    <th>Nom</th>
                    <th>Mail de liaison</th>
                    <th>Billets autorisé</th>
                    <th>Interval de vente</th>
                    <th>Ventes réalisé</th>
                    <th>Lien</th>
                </tr>
                @forelse($guichets as $guichet)
                    <tr>
                        <td>{{ $guichet->id }}</td>
                        <td>{{ $guichet->name }}</td>
                        <td>{{ $guichet->mail }}</td>
                        <td> @if($guichet->type == 'sell') {{ $guichet->getPrices()->implode('name',', ') }} @endif</td>
                        <td>{{ $guichet->start_at }} - {{ $guichet->end_at }}</td>
                        <td>Total: {{ $guichet->billets->count() }}
                            <ul>
                            @foreach($guichet->billets->groupBy('price_id') as $price_id=>$billets)
                                <li>{{ $billets->first()->price->name }} ({{$billets->first()->price->price/100}}€): {{ $billets->count() }}</li>
                            @endforeach
                            </ul>
                        </td>
                        <td>@if($guichet->type == 'sell') <a href="{{ url()->route('get_sell_guichet', ['uuid'=>$guichet->uuid]) }}">{{ url()->route('get_sell_guichet', ['uuid'=>$guichet->uuid]) }}</a>@else <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#phoneConfigModal" data-uuid="{{ \Milon\Barcode\DNS2D::getBarcodePNG(url()->route('get_sell_guichet', ['uuid'=>$guichet->uuid]), 'QRCODE,M') }}"><i class="fa fa-qrcode"></i></button> @endif</td>
                    </tr>
                @empty

                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="phoneConfigModal" tabindex="-1" role="dialog" aria-labelledby="phoneConfigModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Configuration de l'application mobile</h4>
                </div>
                <div class="modal-body">
                    <h3>Utiliser un téléphone</h3>
                    <p>Afin de rendre les tâches de validation plus facile, il est recommandé d'utiliser l'application officielle. Cette derniere permet la vérification via QRCode ou recherche nom / prénom.</p>
                    <p><a href='https://play.google.com/store/apps/details?id=fr.cdautume.billetterie_scanner&pcampaignid=MKT-Other-global-all-co-prtnr-py-PartBadge-Mar2515-1'><img alt='Disponible sur Google Play' height="50px" src='https://play.google.com/intl/en_us/badges/images/generic/fr_badge_web_generic.png'/></a></p>
                    <p>Le QRCode si dessous permet la configuration automatique de l'application.</p>
                    <center>
                        <img src="data:image/png;base64," id="qrCodeModal" alt="" height="100">
                    </center>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('sublayout-js')
    <script src="{{ asset('js/moment-with-locales.min.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap-datetimepicker.min.js') }}"></script>
    <script type="application/javascript">
        $(function () {
            $('#start_date').datetimepicker({
                locale: 'fr'
            });
            $('#end_date').datetimepicker({
                locale: 'fr'
            });
            $("#acl").select2();

            $('#phoneConfigModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget) // Button that triggered the modal
                var uuid = button.data('uuid') // Extract info from data-* attributes
                var modal = $(this)
                modal.find('#qrCodeModal').attr('src', 'data:image/png;base64,'+uuid);
                console.log(uuid);
            })
        });
    </script>
@endsection

@section('sublayout-css')
    @parent
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-datetimepicker.min.css') }}">
@endsection