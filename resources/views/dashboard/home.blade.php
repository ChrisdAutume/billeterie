@extends('layouts.dashboard')

@section('title')
Accueil
@endsection

@section('smalltitle')

@endsection

@section('content')
    <div class="box box-info collapsed-box">
        <div class="box-header with-border">
            <h3 class="box-title">Billet non reçu ?</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
            </div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body">
            <form class="form-horizontal" action="{{ route('billet_resend') }}" method="post" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="form-group">
                    <label for="name" class="col-lg-2 text-right">Votre adresse mail: </label>
                    <div class="col-lg-10">
                        <input class="form-control" type="text" id="mail" name="mail" value="">
                    </div>
                </div>
                <input type="submit" class="btn btn-info form-control" value="Envoyer >" />
            </form>
        </div><!-- /.box-body -->
    </div><!-- /.box -->

    <div class="box box-default">
        <div class="box-body">
            @if($content)
                @markdown($content->text)
            @else
                <p>Page d'accueil vide.</p>
            @endif
        <br />
            @if($priceAvailable)
            <a class="btn btn-info form-control" href="{{ route('addNewItem') }}">Acheter une place !</a>
            @elseif($time)
                <a class="btn btn-danger form-control" href="#">Prochaine(s) place(s) disponible dans {{ $time }}</a>
                @else
                    <button class="btn btn-danger form-control" disabled>Aucun billet de disponnible</button>
                @endif
        </div>
</div>
@endsection
