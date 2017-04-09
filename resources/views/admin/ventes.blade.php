@extends('layouts.dashboard')

@section('title')
    Ventes
@endsection

@section('smalltitle')
    Récapitulatif
@endsection

@section('content')
    <div class="box box-default">

        <div class="box-header with-border">
            <h3 class="box-title">Stats</h3>
        </div>
        <div class="box-body">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>Tarifs</th>
                        <th>Nombres</th>
                        <th>Validés</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($prices as $price)
                            <tr class="vert-align">
                                <td>
                                    <strong>{{ $price->name }}</strong>
                                </td>
                                <td>{{ $price->billets->count() }}</td>
                                <td>{{ $price->billets->count() - $price->billets->where('validated_at', null)->count() }}</td>
                            </tr>
                    @endforeach
                    <tr class="vert-align">
                        <td>
                            <strong>Dons</strong>
                        </td>
                        <td>{{ $dons/100 }} €</td>
                    </tr>
                    </tbody>
                </table>
        </div>
    </div>
@endsection
