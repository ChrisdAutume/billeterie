@extends('layouts.dashboard')

@section('title')
    Liste
@endsection

@section('smalltitle')
    Ajout d'item
@endsection

@section('content')
    <div class="callout callout-info">
        <h4><i class="fa fa-question-circle"></i> Mais qu'est ce qu'une liste ?</h4>
        <p>Une liste est un élement de controle. Elles sont à la base des restrictions appliqué lors de l'achat d'un billet (ex: le billet "membre" est réservé au adresse mail de la liste "staff", les billets "étudiants" sont réservé aux porteurs d'une addresse finissant par utt.fr ...)</p>
    </div>
    <div class="box box-info collapsed-box">

        <div class="box-header with-border">
            <h3 class="box-title">Création d'une liste</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
            </div>
        </div>
        <div class="box-body">
            <form class="form-horizontal" action="" method="post" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="form-group">
                    <label for="list_name" class="col-lg-2 text-right">Nom de la liste</label>
                    <div class="col-lg-10">
                        <input type="text" name="list_name" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label for="list_type_id" class="col-lg-2 text-right">Type de liste</label>
                    <div class="col-lg-10">
                        <select class="form-control" name="list_type_id" id="list_type_id">
                            @foreach(\App\Models\Liste::$typesToString as $key => $liste)
                                <option value="{{ $key }}">{{ $liste}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <input type="submit" class="btn btn-success form-control" value="Créer !" />
            </form>
        </div>
    </div>

    <div class="box box-warning collapsed-box">

        <div class="box-header with-border">
            <h3 class="box-title">Ajout d'élements dans une liste</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
            </div>
        </div>
        <div class="box-body">
            <form class="form-horizontal" action="" method="post" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="form-group">
                    <label for="liste" class="col-lg-2 text-right">Sélection d'une liste</label>
                    <div class="col-lg-10">
                        <select class="form-control" name="liste_id" id="liste">
                            @foreach($lists as $liste)
                                    <option value="{{ $liste->id }}">{{ $liste->name }} ({{ \App\Models\Liste::$typesToString[$liste->type] }})</option>
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