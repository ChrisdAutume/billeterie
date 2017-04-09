@extends('layouts.dashboard')

@section('title')
    {{ $page->name }}
@endsection

@section('smalltitle')
    Mis à jour le {{ $page->updated_at->format('d-m-Y') }}.
@endsection

@section('content')

    <div class="box box-default">
        <div class="box-body">
            @markdown($page->text)
        </div>
    </div>
@endsection
