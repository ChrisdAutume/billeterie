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
            <h3 class="box-title">Stats Tarifs</h3>
        </div>
        <div class="box-body">
            <?php $total =0; ?>
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>Tarifs</th>
                        <th>Vendu</th>
                        <th>Restant</th>
                        <th>Validés</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($prices as $price)
                            <tr class="vert-align">
                                <td>
                                    <strong>{{ $price->name }}</strong>
                                </td>
                                <?php $total += $price->billets->count(); ?>
                                <td>{{ $price->billets->count() }} </td>
                                <td> @if($price->max == 0)Illimité @else {{ $price->max - $price->billetSold(true) }} @endif</td>
                                <td>{{ $price->billets->count() - $price->billets->where('validated_at', null)->count() }}</td>
                            </tr>
                    @endforeach
                    <tr class="vert-align">
                        <td>
                            Totaux
                        </td>
                        <td>{{ $total }} </td>
                        <td></td>
                        <td></td>
                    </tr>
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

    <div class="box box-default">

        <div class="box-header with-border">
            <h3 class="box-title">Stats Options</h3>
        </div>
        <div class="box-body">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>Option</th>
                    <th>Nombres</th>
                    <th>Disponible</th>
                </tr>
                </thead>
                <tbody>
                @foreach($options as $option)
                    <tr class="vert-align">
                        <td>
                            <strong>{{ $option->name }}</strong>
                        </td>
                        <td>{{ $option->billets()->withPivot('qty')->get()->sum('pivot.qty') }}</td>
                        <td>@if($option->max_order == 0) Illimité @else {{ ($option->max_order - $option->billets()->withPivot('qty')->get()->sum('pivot.qty')) }}@endif</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
