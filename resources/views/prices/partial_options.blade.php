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
                    <td><a href="{{ route('admin_prices_lists_delete', ['price'=>$prc, 'liste'=>$liste]) }}" class="btn-flat btn-xs btn-danger"><i class="glyphicon glyphicon-remove"></i></a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <h3 class="box-title">Ajout d'une relation</h3>
        <form class="form-horizontal" action="{{ route('admin_prices_lists_link', ['price' => $prc]) }}" method="post" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="form-group">
                <label for="background" class="col-lg-2 text-right">Liste utilisateur</label>
                <div class="col-lg-10">
                    <select class="form-control select-multiple" id="liste" name="liste">
                        @foreach(\App\Models\Liste::all() as $liste)
                            <option value="{{ $liste->id }}">{{ $liste->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="max" class="col-lg-2 text-right">Utilisation par item de la liste</label>
                <div class="col-lg-10">
                    <input type="number" name="max_order" class="form-control" min="0">
                </div>
            </div>

            <input type="submit" class="btn btn-info form-control" value="Ajout !" />
        </form>
    </div>
</div>