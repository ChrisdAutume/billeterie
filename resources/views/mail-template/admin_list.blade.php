@extends('layouts.dashboard')

@section('title')
    Mail
@endsection

@section('smalltitle')
    Edition des templates mail
@endsection

@section('content')


    <div class="box box-default">

        <div class="box-header with-border">
            <h3 class="box-title">Liste des template</h3>
        </div>
        <div class="box-body">
            <table class="table table-bordered table-hover">
                <tbody><tr>
                    <th>Nom</th>
                    <th>Titre</th>
                    <th>Mise à jour</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                @forelse($templates as $template)
                    <tr>
                        <td>{{ $template->name }}</td>
                        <td>{{ $template->title }}</td>
                        <td>{{ $template->updated_at }}</td>
                        <td>@if($template->isActive)<span class="label label-success">Activé</span> @else <span class="label label-danger">Désactivé</span> @endif</td>
                        <td><a class="btn-flat btn btn-info btn-xs" href="{{ url()->route('edit_mail_template', ['mail-template'=>$template]) }}">Editer</a>
                            @if($template->isActive)
                                <a class="btn-flat btn btn-warning btn-xs" href="{{ url()->route('toogle_mail_template', ['mail-template'=>$template]) }}">Désactiver</a>
                            @else
                                <a class="btn-flat btn btn-success btn-xs" href="{{ url()->route('toogle_mail_template', ['mail-template'=>$template]) }}">Activer</a>
                            @endif
                            <a href="{{ url()->route('view_mail_template', ['mail-template'=>$template]) }}" target="_blank" class="btn btn-xs btn-flat bg-aqua-active"><i class="fa fa-eye"></i></a>
                        </td>
                    </tr>
                @empty
                    <tr>Vide</tr>
                @endforelse
                </tbody></table>
        </div>
    </div>
@endsection