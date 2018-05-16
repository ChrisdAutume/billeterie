@extends('layouts.dashboard')

@section('title')
    Panier
@endsection

@section('smalltitle')
    Récapitulatif
@endsection

@section('content')

    <div class="box box-default">
        <form class="form-horizontal" action="{{ route('postCaddie') }}" method="post" enctype="multipart/form-data">
            {{ csrf_field() }}

        <div class="box-header with-border">
            <h3 class="box-title">Infos acheteur</h3>
        </div>

        <div class="box-body">
                @if(session('billets'))

                <div class="form-group">
                    <label for="name" class="col-lg-2 text-right">Nom</label>
                    <div class="col-lg-10">
                        <input class="form-control" type="text" id="name" name="name" value="{{ session('billets')[0]['billet']->name }}">
                    </div>
                </div>
                <div class="form-group">
                    <label for="surname" class="col-lg-2 text-right">Prénom</label>
                    <div class="col-lg-10">
                        <input class="form-control" type="text" id="surname" name="surname" value="{{ session('billets')[0]['billet']->surname }}">
                    </div>
                </div>


                <div class="form-group">
                    <label for="mail" class="col-lg-2 text-right">Mail</label>
                    <div class="col-lg-10">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                            <input type="email" class="form-control" name="mail" placeholder="Email" value="{{ session('billets')[0]['billet']->mail }}">
                        </div>
                    </div>
                </div>

                <table class="table table-hover" id="basket">
                    <thead>
                    <tr>
                        <th></th>
                        <th></th>
                        <th style="text-align: center">Prix</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $price = 0 ?>

                    @foreach(session('billets') as $row)
                        <?php $sub_price = 0; ?>
                    <tr class="vert-align">
                        <td><strong>{{ $row['billet']->name }} {{ $row['billet']->surname }}</strong></td>
                        <td>
                            <strong>{{ $row['billet']->price->name }}</strong>
                            <p>@foreach($row['options'] as $opt)
                                   {{ $opt['qty'] }}x {{ $opt['option']->name }} ({{($opt['qty'] * $opt['option']->price)/100}} €)<br>
                                    <?php $sub_price += ($opt['qty'] * $opt['option']->price); ?>
                                @endforeach
                            </p>
                        </td>
                        <?php
                            $sub_price += $row['billet']->price->price;
                            $price += $sub_price;
                        ?>
                        <td class="price" style="text-align: center">{{ round($sub_price/100,2) }} €</td>
                    </tr>
                    @endforeach
                    @if(config('billeterie.don.enabled') == true)
                    <tr class="vert-align">
                        <td><strong></strong></td>
                        <td>
                            <strong>{{ config('billeterie.don.name') }}</strong>
                            <p>
                                {{ config('billeterie.don.text') }}
                            </p>
                        </td>

                        <td class="col-sm-4 col-md-2">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-eur"></i></span>
                                <input type="input" class="form-control" name="don" placeholder="{{ config('billeterie.don.min')/100 }}" min="{{ config('billeterie.don.min')/100 }}">
                            </div>
                        </td>

                    </tr>
                    @endif
                    <tr>
                        <td></td>
                        <td></td>
                        <td style="text-align: center"><strong>{{ round($price/100,2) }} €</strong></td>
                    </tr>
                    </tbody>
                </table>
                    @else
                    Aucun billet présent dans votre panier !
                @endif
                <a class="btn btn-info form-control" href="{{ route('addNewItem') }}">Ajouter un autre billet</a>
                <input type="submit" class="btn btn-success form-control" value="Payer !" />
                    <a class="btn btn-danger form-control" href="{{ route('resetCaddie') }}">Vider le panier</a>
            </form>
        </div>
    </div>
@endsection

@section('subpiwik')
    @if(session('billets'))
    @foreach(session('billets') as $row)
    _paq.push(['addEcommerceItem',
    "{{ $row['billet']->price->id }}",
    "{{ $row['billet']->price->name }}",
    ["price"],
    {{ $row['billet']->price->price/100 }},
    1 // (optional, default to 1) Product quantity
    ]);
        @foreach($row['options'] as $opt)
        _paq.push(['addEcommerceItem',
        "{{ $opt['option']->id }}",
        "{{ $opt['option']->name }}",
        ["options"],
        {{ $opt['option']->price/100 }},
        {{ $opt['qty'] }} // (optional, default to 1) Product quantity
        ]);
        @endforeach
    @endforeach
    _paq.push(['trackEcommerceCartUpdate',
    {{ round($price/100,2) }}]);
    @endif
@endsection