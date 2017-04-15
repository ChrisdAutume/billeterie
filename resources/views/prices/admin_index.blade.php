@extends('layouts.dashboard')

@section('title')
    Tarifs
@endsection

@section('smalltitle')
    gestion
@endsection

@section('content')

    <div class="box box-danger collapsed-box">

        <div class="box-header with-border">
            <h3 class="box-title">Création d'un tarif</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
            </div>
        </div>
        <div class="box-body">
            <form class="form-horizontal" action="" method="post" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="form-group">
                    <label for="liste" class="col-lg-2 text-right">Nom du tarif</label>
                    <div class="col-lg-10">
                        <input type="text" name="name" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label for="description" class="col-lg-2 text-right">Description</label>
                    <div class="col-lg-10">
                        <input type="text" name="description" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label for="price" class="col-lg-2 text-right">Tarif (en centimes)</label>
                    <div class="col-lg-10">
                        <div class="input-group">
                            <input type="text" name="price" class="form-control">
                            <span class="input-group-addon"><i class="glyphicon-euro"></i></span>
                        </div>
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
                <div class="form-group">
                    <label for="max" class="col-lg-2 text-right">Quantitée</label>
                    <div class="col-lg-10">
                        <input type="number" name="max" class="form-control" value="0" min="0">
                    </div>
                </div>
                <div class="form-group">
                    <label for="liste" class="col-lg-2 text-right">Aggrégation de quantité</label>
                    <div class="col-lg-10">
                        <select class="form-control select-multiple" id="acl" multiple="multiple" name="agregat_price[]">
                            @foreach($prices as $price)
                                <option value="{{ $price->id }}">{{ $price->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="options" class="col-lg-2 text-right">Option du billet</label>
                    <div class="col-lg-10">
                        <select class="form-control select-multiple" id="options" multiple="multiple" name="options[]">
                            @foreach($options as $option)
                                <option value="{{ $option->id }}">{{ $option->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="max" class="col-lg-2 text-right">Fonctionnalitées</label>
                    <div class="col-lg-10">
                        <label><input type="checkbox" name="sendBillet" value="1"> Envoi d'un billet</label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="background" class="col-lg-2 text-right">Fond du billet</label>
                    <div class="col-lg-10">
                        <input type="text" name="background" class="form-control">
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
                    <th>Prix</th>
                    <th>Interval de vente</th>
                    <th>Lien</th>
                </tr>
                @foreach($prices as $price)
                    <tr>
                        <td>{{ $price->id }}</td>
                        <td>{{ $price->name }}</td>
                        <td>{{ $price->price/100 }} €</td>
                        <td>{{ $price->start_at }} - {{ $price->end_at }}</td>
                        <td><a href="{{ route('admin_prices_edit', ['price'=>$price]) }}" class="btn-flat btn-xs btn-warning"><i class="glyphicon glyphicon-edit"></i></a></td>
                    </tr>
                @endforeach
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
            $(".select-multiple").select2();
        });
    </script>
@endsection

@section('sublayout-css')
    @parent
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-datetimepicker.min.css') }}">
@endsection