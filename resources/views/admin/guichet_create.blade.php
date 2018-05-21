@extends('layouts.dashboard')

@section('title')
    Guichet
@endsection

@section('smalltitle')
    gestion
@endsection

@section('content')

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
                    <label for="liste" class="col-lg-2 text-right">Billets autorisé</label>
                    <div class="col-lg-10">
                        <select class="form-control" id="acl" multiple="multiple" name="acl[]">
                            @foreach($prices as $price)
                                <option value="{{ $price->id }}">{{ $price->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="liste" class="col-lg-2 text-right">Date de début de vente</label>
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
                    <label for="liste" class="col-lg-2 text-right">Date de fin de vente
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

                        </td>
                        <td><a href="{{ url()->route('get_sell_guichet', ['uuid'=>$guichet->uuid]) }}">{{ url()->route('get_sell_guichet', ['uuid'=>$guichet->uuid]) }}</a></td>
                    </tr>
                @empty

                @endforelse
                </tbody>
            </table>
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
        });
    </script>
@endsection

@section('sublayout-css')
    @parent
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-datetimepicker.min.css') }}">
@endsection