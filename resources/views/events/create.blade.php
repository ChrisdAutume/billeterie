@extends('layouts.dashboard')

@section('title')
    Creation d'un évènement
@endsection

@section('content')

    <div class="callout callout-info">
        <h4>Création d'un nouvel évènements</h4>
        <p>
            Pour créér un évènement, choisissez une date de début ainsi qu'une date de fin (date et heure). Renseignez un nom
            et une description qui définissent l'évènement, ainsi que l'endroit où il aura lieu. Pour finir, vous devez ajouter 
            un lien vers une image pour cet événement
        </p>
    </div>

    <div class="box box-default">
        <div class="box-body table-responsive">
            <form action="{{ url('admin/event') }}" method="post">
                {{ csrf_field() }}
                <div class="form-group">
                    <label for="name">Nom</label>
                    <input type="text" class="form-control" name="name" value="{{ old('name') }}">
                </div>
                <div class="form-group">
                    <label for="name">Description</label>
                    <textarea name="description" class="form-control">{{ old('description') }}</textarea>
                </div>
                <div class="form-group">
                    <label for="place">Lieu</label>
                    <input type="text" class="form-control" name="place" value="{{ old('place') }}">
                </div>
                <div class="form-group">
                    <label>Début (date et heure)</label>
                    <input type="date" class="form-control" value="{{ old('start_at_date') }}" name="start_at_date">
                    <input type="time" class="form-control" value="{{ old('start_at_hour') }}" name="start_at_hour">
                </div>
                <div class="form-group">
                    <label>Fin (date et heure)</label>
                    <input type="date" class="form-control" value="{{ old('end_at_date') }}" name="end_at_date">
                    <input type="time" class="form-control" value="{{ old('end_at_hour') }}" name="end_at_hour">
                </div>
                <div class="form-group">
                    <label for="image">Image</label>
                    <input type="text" class="form-control" name="image" value="{{ old('image') }}">
                </div>

                <button type="submit" class="btn btn-success">Créer l'évènement</button>
            </form>
        </div>
    </div>

@endsection
