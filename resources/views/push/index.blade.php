@extends('layouts.dashboard')

@section('title')
    Envoie d'une notification
@endsection

@section('content')

    <div class="callout callout-info">
        <h4>Création d'une notification</h4>
        <p>
            Pour créér une notification, renseigner son titre et son contenue. Attention à ne pas faire de message trop long.
        </p>
    </div>

    <div class="box box-default">
        <div class="box-body table-responsive">
            <form action="{{ url('admin/notifications') }}" method="post">
                {{ csrf_field() }}
                <div class="form-group">
                    <label for="title">Titre</label>
                    <input type="text" class="form-control" name="title" value="{{ old('title') }}">
                </div>
                <div class="form-group">
                    <label for="content">Contenu</label>
                    <input type="text" class="form-control" name="content" value="{{ old('content') }}">
                </div>

                <button type="submit" class="btn btn-success">Envoyer la notification</button>
            </form>
        </div>
    </div>

@endsection
