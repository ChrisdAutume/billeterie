@extends('layouts.dashboard')

@section('title')
    Liste
@endsection

@section('smalltitle')
    Ajout d'item
@endsection

@section('content')

    <div class="box box-default">

        <div class="box-header with-border">
            <h3 class="box-title">Ajout d'élement dans une liste</h3>
        </div>
        <div class="box-body">
            <form class="form-horizontal" action="" method="post" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="form-group">
                    <label for="liste" class="col-lg-2 text-right">Sélection d'une liste</label>
                    <div class="col-lg-10">
                        <select class="form-control" name="liste_id" id="liste">
                            @foreach($lists as $liste)
                                    <option value="{{ $liste->id }}">{{ $liste->name }} ({{ $liste->type }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="content" class="col-lg-2 text-right">Contenu</label>
                    <div class="col-lg-10">
                        <textarea class="form-control" rows="5" name="content" placeholder="En cas d'ajout de multiple item, séparer chaque item par une virgule (,)"></textarea>
                    </div>
                </div>
                <input type="submit" class="btn btn-success form-control" value="Ajouter !" />
            </form>
        </div>
    </div>

    <div class="box box-default">

        <div class="box-header with-border">
            <h3 class="box-title">Contenu des listes</h3>
        </div>
        <div class="box-body">
            <table class="table table-bordered table-hover">
                <tbody><tr>
                    <th style="width: 10px">#</th>
                    <th>Nom</th>
                    <th>Contenu</th>
                </tr>
                @forelse($lists as $liste)
                <tr>
                    <td>{{ $liste->id }}</td>
                    <td>{{ $liste->name }}</td>
                    <td>@foreach($liste->itemList as $item){{ $item->value }}, @endforeach</td>
                </tr>
                    @empty
                    <tr>Vide</tr>
                @endforelse
                </tbody></table>
        </div>
    </div>
@endsection