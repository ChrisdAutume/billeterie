@extends('layouts.dashboard')

@section('title')
    Fichiers
@endsection

@section('smalltitle')
    Gestionnaire
@endsection

@section('content')
    <div class="box box-default">

        <div class="box-header with-border">
            <h3 class="box-title">Ajout de fichier</h3>
        </div>
        <div class="box-body">
            <form class="form-horizontal" action="{{ url()->route('upload_file') }}" method="post" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="form-group">
                    <label for="file">Ajout d'un fichier</label>
                    <input type="file" id="file" name="file">
                    <p class="help-block">Seul les fichiers suivants sont acceptés: jpeg,bmp,png,pdf</p>
                </div>

                <input type="submit" class="btn btn-success form-control" value="Ajouter !" />
            </form>
        </div>
    </div>

    <div class="box">
        <div class="box-header">
            <h3 class="box-title">Liste des fichiers</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>Nom du fichier</th>
                    <th>Taille</th>
                    <th>Création</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach($files as $file)
                    <tr>
                        <td>{{ $file->name }}</td>
                        <td>{{ $file->size }}</td>
                        <td>{{ $file->created_at->format('d/m/Y H:s') }}</td>
                        <td><a class="btn btn-xs btn-flat btn-info" href="{{ route('view_file', ['file'=>$file]) }}"><i class="fa fa-eye"></i></a>
                            <a class="btn btn-xs btn-flat btn-danger" href="{{ route('delete_file', ['file'=>$file]) }}"><i class="fa fa-trash"></i></a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr>
                    <th>Nom du fichier</th>
                    <th>Taille</th>
                    <th>Création</th>
                    <th>Action</th>
                </tr>
                </tfoot>
            </table>
        </div>
        <!-- /.box-body -->
    </div>
@endsection

