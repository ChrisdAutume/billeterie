@extends('layouts.dashboard')

@section('title')
    Creation d'un partenaire
@endsection

@section('content')

    <div class="callout callout-info">
        <h4>Création d'un nouveau partenaire</h4>
        <p>
            Pour créér un partenaire, renseigner son nom, le lien vers son site, et le lien de l'image à afficher.
        </p>
    </div>

    <div class="box box-default">
        <div class="box-body table-responsive">
            <form action="{{ url('admin/partner') }}" method="post">
                {{ csrf_field() }}
                <div class="form-group">
                    <label for="name">Nom</label>
                    <input type="text" class="form-control" name="name" value="{{ old('name') }}">
                </div>
                <div class="form-group">
                    <label for="name">Lien (ne pas oublier le protocole http:// ou https:// si besoin)</label>
                    <textarea name="link" class="form-control">{{ old('link') }}</textarea>
                </div>
                <div class="form-group">
                    <label for="image">Image</label>
                    <input type="text" class="form-control" name="image" value="{{ old('image') }}">
                </div>

                <button type="submit" class="btn btn-success">Créer le partenaire</button>
            </form>
        </div>
    </div>

@endsection
