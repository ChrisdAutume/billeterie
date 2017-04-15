<div class="box box-default" id="option_edit">

    <div class="box-header with-border">
        <h3 class="box-title">Gestion des listes</h3>
    </div>
    <div class="box-body">
        <table class="table table-bordered table-hover">
            <tbody><tr>
                <th style="width: 10px">#</th>
                <th>Nom</th>
                <th>Nombre de commande max</th>
                <th></th>
            </tr>
            @foreach($prc->lists as $liste)
                <tr>
                    <td>{{ $liste->id }}</td>
                    <td>{{ $liste->name }}</td>
                    <td>{{ $liste->pivot->max_order }} </td>
                    <td><a href="{{ route('admin_prices_lists_delete', ['price'=>$price, 'liste'=>$liste]) }}" class="btn-flat btn-xs btn-danger"><i class="glyphicon glyphicon-remove"></i></a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>