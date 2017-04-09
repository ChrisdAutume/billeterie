@extends('layouts.dashboard')

@section('title')
    Billet
@endsection

@section('smalltitle')
    Ajout d'un nouveau billet
@endsection

@section('content')

    <div class="callout callout-info">
        <h4>Attention !</h4>
        <p>Pour bénéficier de tarifs réduits, pensez à utiliser l’adresse de votre école, dans le cas échéant contactez <a href="mailto:{{ config('billeterie.contact') }}">{{ config('billeterie.contact') }}</a>.</p>
    </div>

    <div class="box box-default">

        <div class="box-header with-border">
            <h3 class="box-title">Coordonnée</h3>
        </div>
        <div class="box-body">
            <form class="form-horizontal" action="" method="post" enctype="multipart/form-data">
                {{ csrf_field() }}
                    <div class="form-group">
                        <label for="name" class="col-lg-2 text-right">Nom</label>
                        <div class="col-lg-10">
                            <input class="form-control" type="text" id="name" name="name" value="{{ old('name') }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="surname" class="col-lg-2 text-right">Prénom</label>
                        <div class="col-lg-10">
                            <input class="form-control" type="text" id="surname" name="surname" value="{{ old('surname') }}">
                        </div>
                    </div>


                    <div class="form-group">
                        <label for="mail" class="col-lg-2 text-right">Mail</label>
                        <div class="col-lg-10">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                                <input type="email" class="form-control" name="mail" placeholder="Email" value="{{ old('mail') }}">
                            </div>
                        </div>
                    </div>
                <input type="submit" class="btn btn-success form-control" value="Suivant !" />
            </form>
        </div>
    </div>
@endsection
