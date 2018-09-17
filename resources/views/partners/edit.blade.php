@extends('layouts.dashboard')

@section('title')
    Modification d'un partenaire
@endsection

@section('smalltitle')
    {{ $partner->name }}
@endsection

@section('content')

    <div class="box box-default">
        <div class="box-body table-responsive">
            <form action="{{ url('admin/partner/'.$partner->id) }}" method="post">
                {{ csrf_field() }}
                {{ method_field('PUT') }}
                <div class="form-group">
                    <label for="name">Nom</label>
                    <input type="text" class="form-control" name="name" value="{{ $partner->name }}">
                </div>
                <div class="form-group">
                    <label for="place">Lien</label>
                    <input type="text" class="form-control" name="link" value="{{ $partner->link }}">
                </div>
                <div class="form-group">
                  <label for="image">Image</label>
                  <input type="text" class="form-control" name="image" value="{{ $partner->image }}">
              </div>
                <button type="submit" class="btn btn-success">Modifier l'évènement</button>
            </form>
        </div>
    </div>

@endsection
