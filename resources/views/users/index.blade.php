@extends('layouts.dashboard')

@section('title')
    Utilisateur
@endsection

@section('smalltitle')
    Récapitulatif
@endsection

@section('content')
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">Utilisateur</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <table id="users" class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>id</th>
                    <th>Nom</th>
                    <th>Mail</th>
                    <th>Niveau</th>
                    <th>Dernier Login</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->mail }}</td>
                        <td>{{ $user->level }}</td>
                        <td>{{ $user->last_login }}</td>
                        <td>@if($user->level<100)
                                <a href="{{ route('admin_users_level', ['level'=> 100, 'admin' => $user]) }}" class="btn btn-xs btn-flat btn-danger">Convertir en admin</a>
                            @else
                                <a href="{{ route('admin_users_level', ['level'=> 0, 'admin' => $user]) }}" class="btn btn-xs btn-flat btn-danger">Convertir en utilisateur</a>
                            @endif</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <!-- /.box-body -->
    </div>
@endsection
@section('js')
    @parent
    <script src="{{ @asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ @asset('plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
@endsection
@section('sublayout-js')
    <script>
        $(function () {
            $("#users").DataTable();
        });
    </script>
@endsection

