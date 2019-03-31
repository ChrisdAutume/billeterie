@extends('layouts.dashboard')

@section('title')
    Partenaires
@endsection

@section('smalltitle')
    Liste de tous les partenaires de la soirée.
@endsection

@section('content')

    <div class="callout callout-info">
        <h4>Liste des partenaires</h4>
        <p>
            Un partenaire a un nom, un lien vers son site et une image (qui est un lien vers l'image).
        </p>
    </div>

    <div class="box-header with-border">
        <h3 class="box-title">Création d'un nouveau partenaire</h3>
        <a href="{{ url('admin/partner/create') }}" class="btn btn-box-tool">
            <i class="fa fa-plus"></i>
        </a>
    </div>

    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title">Liste des partenaires</h3>
        </div>
        <div class="box-body table-responsive no-padding">
            <table class="table table-hover">
                <tbody>
                    <tr>
                        <th>Nom</th>
                        <th>Lien</th>
                        <th>Image</th>
                        <th>Actions</th>
                    </tr>
                    @foreach ($partners as $partner)
                        <tr>
                            <td>{{ $partner->name }}</td>
                            <td>{{ $partner->link }}</td>
                            <td>{{ $partner->image }}</td>
                            <td>
                                <a class="btn btn-xs btn-warning" href="{{ url('admin/partner/edit/'.$partner->id) }}">Modifier</a>
                                <form action="{{ url('admin/partner/'.$partner->id) }}" method="post">
                                    {{ csrf_field() }}
                                    {{ method_field('DELETE') }}
                                    <button class="btn btn-xs btn-danger" type="submit">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection
