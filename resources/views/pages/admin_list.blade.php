@extends('layouts.dashboard')

@section('title')
    Pages
@endsection

@section('smalltitle')
    Liste des pages
@endsection

@section('content')


    <div class="box box-default">

        <div class="box-header with-border">
            <h3 class="box-title">Liste des pages</h3>
        </div>
        <div class="box-body">
            <a class="btn-flat btn btn-info" href="{{ url()->route('create_page') }}">Création d'une page</a>
            <table class="table table-bordered table-hover">
                <tbody><tr>
                    <th style="width: 10px">#</th>
                    <th>Nom</th>
                    <th>Mise à jour</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                @forelse($pages as $page)
                    <tr>
                        <td>{{ $page->id }}</td>
                        <td>{{ $page->name }} </td>
                        <td>{{ $page->updated_at }}</td>
                        <td>@if($page->published)<span class="label label-success">Publié</span> @else <span class="label label-warning">En attente</span> @endif</td>
                        <td><a class="btn-flat btn btn-info btn-xs" href="{{ url()->route('edit_page', ['page'=>$page]) }}">Editer</a>
                            @if($page->published)
                                <a class="btn-flat btn btn-warning btn-xs" href="{{ url()->route('toogle_page', ['page'=>$page]) }}">Dé-publier</a>
                            @else
                                <a class="btn-flat btn btn-success btn-xs" href="{{ url()->route('toogle_page', ['page'=>$page]) }}">Publier</a>
                            @endif
                            @if(!$page->home)
                                <a href="{{ url()->route('set_homepage_page', compact('page')) }}" class="btn btn-xs btn-flat btn-danger">Désigner en page d'accueil</a>
                            @endif
                            <a href="{{ url()->route('view_page', compact('page')) }}" class="btn btn-xs btn-flat bg-aqua-active"><i class="fa fa-eye"></i></a>
                        </td>
                    </tr>
                @empty
                    <tr>Vide</tr>
                @endforelse
                </tbody></table>
        </div>
    </div>
@endsection