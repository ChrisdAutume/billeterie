@extends('layouts.dashboard')

@section('title')
    Commande
@endsection

@section('smalltitle')
    Récapitulatif
@endsection

@section('content')
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">Commandes</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <table id="commandes" class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>id</th>
                    <th>Acheteur</th>
                    <th>Places</th>
                    <th>Don</th>
                    <th>Statut</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach($orders as $order)
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->name }} {{ $order->surname }}</td>
                        <td><ul>
                                @foreach($order->billets as $billet)
                                    <li>{{ $billet->name }} {{ $billet->surname }} ({{ $billet->price->name }}) <a
                                                class="btn btn-success btn-xs" href="{{ route('admin_billet_mail', ['id'=>$billet->id]) }}"><i class="fa fa-envelope"></i></a>
                                        <a
                                                class="btn btn-info btn-xs" href="{{ route('admin_billet_view', ['billet'=>$billet]) }}"><i class="fa fa-eye"></i></a>
                                        @if(is_null($billet->validated_at))<a
                                                class="btn btn-info btn-xs" href="{{ route('admin_billet_validate', ['id'=>$billet->id]) }}"><i class="fa fa-ticket"></i> Valider l'entrée</a>
                                    @endif </li>
                                @endforeach
                            </ul></td>
                        <td>@if($order->dons->count() > 0) {{ $order->dons[0]->amount/100 }} € @else <i>Aucun</i> @endif</td>
                        <td>{{ $order->state }}</td>
                        <td><a class="btn btn-xs btn-flat btn-warning" href="{{ route('admin_order_edit', ['order'=>$order]) }}"><i class="fa fa-pencil"></i></a></td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr>
                    <th>id</th>
                    <th>Acheteur</th>
                    <th>Places</th>
                    <th>Don</th>
                    <th>Statut</th>
                    <th>Action</th>
                </tr>
                </tfoot>
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
            $("#commandes").DataTable();
        });
    </script>
@endsection

