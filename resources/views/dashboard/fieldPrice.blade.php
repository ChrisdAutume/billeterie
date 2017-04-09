@extends('layouts.dashboard')

@section('title')
    Billet
@endsection

@section('smalltitle')
    Ajout d'un nouveau billet
@endsection

@section('content')

    <div class="box box-default">

        <div class="box-header with-border">
            <h3 class="box-title">Information complémentaire</h3>
        </div>
        <div class="box-body">
            <form class="form-horizontal" action="" method="post" enctype="multipart/form-data">
                {{ csrf_field() }}
                @foreach($fields as $field)
                <div class="form-group">
                    <label for="field_{{ $field->id }}" class="col-lg-2 text-right">{{ $field->name }}</label>
                    <div class="col-lg-10">
                        @if($field->type == 'input')
                            <input class="form-control" type="text" id="field_{{ $field->id }}" name="field_{{ $field->id }}" value="{{ old('field_'.$field->id) }}" placeholder="{{ $field->default }}">
                        @elseif($field->type == 'text')
                            <textarea class="form-control" name="field_{{ $field->id }}" rows="10" id="field_{{ $field->id }}" placeholder="{{ $field->default }}">{{ old('field_'.$field->id) }}</textarea>
                        @endif
                    </div>
                </div>
                @endforeach
                <input type="submit" class="btn btn-success form-control" value="Suivant !" />
            </form>
        </div>
    </div>
@endsection
