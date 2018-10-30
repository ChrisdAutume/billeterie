@extends('layouts.dashboard')

@section('title')
    Modification d'un évènement
@endsection

@section('smalltitle')
    {{ $event->name }}
@endsection

@section('content')

    <div class="box box-default">
        <div class="box-body table-responsive">
            <form action="{{ url('admin/event/'.$event->id) }}" method="post">
                {{ csrf_field() }}
                {{ method_field('PUT') }}
                <div class="form-group">
                    <label for="name">Nom</label>
                    <input type="text" class="form-control" name="name" value="{{ $event->name }}">
                </div>
                <div class="form-group">
                    <label for="name">Description</label>
                    <textarea name="description" class="form-control">{{ $event->description }}</textarea>
                </div>
                <div class="form-group">
                    <label for="place">Lieu</label>
                    <input type="text" class="form-control" name="place" value="{{ $event->place }}">
                </div>
                <div class="form-group">
                    <label>Début (date et heure)</label>
                    <input type="date" class="form-control" value="{{ date('Y-m-d', $event->start_at)  }}" name="start_at_date">
                    <input type="time" class="form-control" value="{{ date('H:i', $event->start_at) }}" name="start_at_hour">
                </div>
                <div class="form-group">
                    <label>Fin (date et heure)</label>
                    <input type="date" class="form-control" value="{{ date('Y-m-d', $event->end_at) }}" name="end_at_date">
                    <input type="time" class="form-control" value="{{ date('H:i', $event->end_at) }}" name="end_at_hour">
                </div>
                <div class="form-group">
                  <label for="image">Image</label>
                  <input type="text" class="form-control" name="image" value="{{ $event->image }}">
              </div>
                <button type="submit" class="btn btn-success">Modifier l'évènement</button>
            </form>
        </div>
    </div>

@endsection
