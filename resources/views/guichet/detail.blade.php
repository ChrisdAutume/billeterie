<div class="box box-default">

    <div class="box-header with-border">
        <h3 class="box-title">Billet n°<span id='billet_id'></span></h3>
    </div>
    <div class="box-body">
        <div class="form-group">
            <label for="name" class="col-lg-2 text-right">Nom</label>
            <div class="col-lg-10">
                <input class="form-control" type="text" id="name" name="name" value="{{ old('name') }}">
            </div>
        </div>
        <div class="form-group">
            <label for="surname" class="col-lg-2 text-right">Prénom</label>
            <div class="col-lg-10">
                <input class="form-control" type="text" id="surname" name="surname" value="{{ old('surname') }}">
            </div>
        </div>
    </div>
</div>