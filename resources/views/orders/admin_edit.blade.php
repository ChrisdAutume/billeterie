@extends('layouts.dashboard')

@section('title')
    Edition commande
@endsection

@section('smalltitle')
    n°{{ $order->id }} - Crée {{ $order->created_at }} Mis à jour: {{ $order->updated_at }}
@endsection

@section('content')
    <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon @if($order->state == 'paid') bg-green @else bg-red @endif"><i class="fa fa-euro"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Paiement</span>
                    <span class="info-box-number">{{ number_format($order->price/100, 2, ',', ' ')}} €</span>
                    <span class="info-box-text">{{ \App\Models\Order::$states[$order->state] }} @if($order->mean_of_paiment) - {{ \App\Models\Order::$means[$order->mean_of_paiment] }} @endif</span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
    </div>
    <div class="box box-default">

        <div class="box-header with-border">
            <h3 class="box-title">Coordonnée acheteur</h3>
        </div>
        <div class="box-body">
            <form class="form-horizontal" action="{{ route('post_admin_order_edit', ['order'=>$order]) }}" method="post" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="form-group">
                    <label for="name" class="col-lg-2 text-right">Nom</label>
                    <div class="col-lg-10">
                        <input class="form-control" type="text" id="name" name="name" value="{{ $order->name }}">
                    </div>
                </div>
                <div class="form-group">
                    <label for="surname" class="col-lg-2 text-right">Prénom</label>
                    <div class="col-lg-10">
                        <input class="form-control" type="text" id="surname" name="surname" value="{{ $order->surname }}">
                    </div>
                </div>


                <div class="form-group">
                    <label for="mail" class="col-lg-2 text-right">Mail</label>
                    <div class="col-lg-10">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                            <input type="email" class="form-control" name="mail" placeholder="Email" value="{{ $order->mail }}">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="mail" class="col-lg-2 text-right">Montant</label>
                    <div class="col-lg-10">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-euro"></i></span>
                            <input type="number" class="form-control" name="mail" placeholder="Montant" value="{{ $order->price }}">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="state" class="col-lg-2 text-right">Status de la commande</label>
                    <div class="col-lg-10">
                        <select name="state" class="form-control" id="state">
                            @foreach(\App\Models\Order::$states as $state=>$state_name)
                                <option value="{{ $state }}" @if($order->state == $state) selected @endif>{{ $state_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="mean_of_paiment" class="col-lg-2 text-right">Moyen de paiment</label>
                    <div class="col-lg-10">
                        <select name="mean_of_paiment" class="form-control" id="mean_of_paiment">
                            <option value="">Aucun</option>
                            @foreach(\App\Models\Order::$means as $mean=>$mean_name)
                                <option value="{{ $mean }}" @if($order->mean_of_paiment == $mean) selected @endif>{{ $mean_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <input type="submit" class="btn btn-success form-control" value="Modifier" />
            </form>
        </div>

        @foreach($order->billets as $billet)
        <div class="box-header with-border">
            <h3 class="box-title">Billet n°{{ $billet->uuid }}</h3>
            <div class="box-tools pull-right">
                <div class="btn-group">
                    <a href="{{ route('admin_billet_delete', ['billet' => $billet]) }}" class="btn btn-box-tool"><i class="fa fa-close"></i>Supprimer</a>
                </div>
            </div>
        </div>
        <div class="box-body">
            <form class="form-horizontal" action="{{ route('post_admin_billet_edit', ['billet'=>$billet]) }}" method="post" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="form-group">
                    <label for="name" class="col-lg-2 text-right">Nom</label>
                    <div class="col-lg-10">
                        <input class="form-control" type="text" id="name" name="name" value="{{ $billet->name }}">
                    </div>
                </div>
                <div class="form-group">
                    <label for="surname" class="col-lg-2 text-right">Prénom</label>
                    <div class="col-lg-10">
                        <input class="form-control" type="text" id="surname" name="surname" value="{{ $billet->surname }}">
                    </div>
                </div>


                <div class="form-group">
                    <label for="mail" class="col-lg-2 text-right">Mail</label>
                    <div class="col-lg-10">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                            <input type="email" class="form-control" name="mail" placeholder="Email" value="{{ $billet->mail }}">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="tarif" class="col-lg-2 text-right">Tarif</label>
                    <div class="col-lg-10">
                        <select name="price_id" class="form-control" id="tarif">
                            @foreach($prices as $price)
                                <option value="{{ $price->id }}" @if($price->id == $billet->price_id) selected @endif>{{ $price->name }} ({{ $price->price/100 }}€)</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-lg-2 text-right">Options</label>
                    <div class="col-lg-10">
                        <ul>
                            @foreach($billet->options as $option)
                                <li><b>{{ $option->pivot->qty }}</b> {{ $option->name }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <input type="submit" class="btn btn-info form-control" value="Modifier" />
            </form>
        </div>
        @endforeach
    </div>
@endsection
