@extends('layouts.dashboard')

@section('title')
    Évènements
@endsection

@section('smalltitle')
    Liste de tous les évènements de la soirée.
@endsection

@section('content')

    <div class="callout callout-info">
        <h4>Liste des événements</h4>
        <p>
            Un évènement a une date de début, une date de fin, un lieu et une image (qui est un lien vers l'image).
        </p>
    </div>

    <div class="box-header with-border">
        <h3 class="box-title">Création d'un nouvel évènement</h3>
        <a href="{{ url('admin/event/create') }}" class="btn btn-box-tool">
            <i class="fa fa-plus"></i>
        </a>
    </div>

    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title">Liste des évènements</h3>
        </div>
        <div class="box-body table-responsive no-padding">
            <table class="table table-hover">
                <tbody>
                    <tr>
                        <th>Nom</th>
                        <th>Lieu</th>
                        <th>Début</th>
                        <th>Fin</th>
                        <th>Image</th>
                        <th>Actions</th>
                    </tr>
                    @foreach ($events as $event)
                        <tr>
                            <td>{{ $event->name }}</td>
                            <td>{{ $event->place }}</td>
                            <td>{{ date('d/m H:i', $event->start_at) }}</td>
                            <td>{{ date('d/m H:i', $event->end_at) }}</td>
                            <td>{{ $event->image }}</td>
                            <td>
                                <a class="btn btn-xs btn-warning" href="{{ url('admin/event/edit/'.$event->id) }}">Modifier</a>
                                <form action="{{ url('admin/event/'.$event->id) }}" method="post">
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
