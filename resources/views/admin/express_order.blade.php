@extends('layouts.dashboard')

@section('title')
    Guichet
@endsection

@section('smalltitle')
    Vente express
@endsection

@section('content')

    <div class="box box-default">

        <div class="box-header with-border">
            <h3 class="box-title">Commande express</h3>
        </div>
        <div class="box-body">
            @foreach($prices as $price)
                <a href="{{ route('admin_express_orders', ['price_id'=>$price->id]) }}" type="submit" class="btn btn-success form-control">{{ $price->name }} - <strong>{{ $price->price/100 }}€</strong></a>
            @endforeach
        </div>
    </div>

@endsection
