@extends('layouts.dashboard')

@section('title')
    Commande
@endsection

@section('smalltitle')
    Création
@endsection

@section('content')
    @if(isset($guichet))
        <div class="row">
            <div class="col-lg-8 col-xs-8">
                <div class="callout callout-info">
                    <h4>Guichet {{ $guichet->name }}</h4>
                    <p>Votre guichet fermera le <strong>{{ $guichet->end_at }}</strong>. <br> Vous pouvez signaler le moindre soucis en contactant <a href="mailto:{{ config('billeterie.contact') }}">{{ config('billeterie.contact') }}</a> .</p>
                </div>
            </div>
            <div class="col-lg-4 col-xs-4">
                <!-- small box -->
                <div class="small-box bg-green-active">
                    <div class="inner">
                        <h3>{{ $guichet->billets->count() }}</h3>

                        <p>Billets vendu via ce guichet</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-shopping-cart"></i>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="callout callout-danger">
            <h4><i class="fa fa-warning"></i> Création en mode ADMIN</h4>
            <p>Il est déconseillé de créer des commandes par cette page dans le cas de "ventes". La création de guichet est plus à même de répondre au besoin !</p>
            <p>A réserver pour la création de billets exceptionnel (partenaires, ventes gratuite ... )</p>
        </div>
    @endif

    <div class="box box-default">

        <div class="box-header with-border">
            <h3 class="box-title">Coordonnée de l'acheteur</h3>
        </div>
        <div class="box-body" id="buyer">
            <form class="form-horizontal" action="" method="post" enctype="application/x-www-form-urlencoded">
                {{ csrf_field() }}
                <div class="form-group">
                    <label for="name" class="col-lg-2 text-right">Nom</label>
                    <div class="col-lg-10">
                        <input class="form-control" type="text" id="name" name="buyer_name" value="{{ old('buyer_name') }}">
                    </div>
                </div>
                <div class="form-group">
                    <label for="surname" class="col-lg-2 text-right">Prénom</label>
                    <div class="col-lg-10">
                        <input class="form-control" type="text" id="surname" name="buyer_surname" value="{{ old('surname[]') }}">
                    </div>
                </div>


                <div class="form-group">
                    <label for="mail" class="col-lg-2 text-right">Mail</label>
                    <div class="col-lg-10">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                            <input type="email" class="form-control" name="buyer_mail" id="mail" placeholder="Email" value="{{ old('surname[]') }}">
                        </div>
                    </div>
                </div>
                <fieldset id="billet1" class="billet">
                    <legend id="form">Billet #1</legend>
                    <div class="form-group">
                        <label for="name" class="col-lg-2 text-right">Nom</label>
                        <div class="col-lg-10">
                            <input class="form-control" type="text" id="name" name="name[]" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="surname" class="col-lg-2 text-right">Prénom</label>
                        <div class="col-lg-10">
                            <input class="form-control" type="text" id="surname" name="surname[]" value="">
                        </div>
                    </div>


                    <div class="form-group">
                        <label for="mail" class="col-lg-2 text-right">Mail</label>
                        <div class="col-lg-10">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                                <input type="email" class="form-control" id='mail' name="mail[]" placeholder="Email" value="">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-2 text-right">Type de billet</label>
                        <div class="col-lg-10">
                            <select class="form-control" name="price[]">
                                @foreach($prices as $price)
                                    <option price="{{ $price->price/100 }}" value="{{ $price->id }}">{{ $price->name }} ({{ $price->price/100 }}€)</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    @foreach($prices as $price)
                        @if( ($options = $price->optionsSellable()->orderBy('name')->get()) && count($options) > 0)
                    <div class="form-group options_prices price_options_{{ $price->id }}">
                        <div class="col-lg-10">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>Options</th>
                                    <th>Prix</th>
                                </tr>
                                </thead>
                            @foreach($options as $option)
                                <tr class="vert-align price price_{{ $price->id }}">
                                    <td>
                                        <select name="option_1_{{ $price->id }}_{{ $option->id }}" class="options_selector" price="{{ $option->price/100 }}" @if($option->available() == 0 || $option->isMandatory) disabled @endif>
                                            @for($i=0; $i <= (($option->max_choice>$option->available())?$option->available():$option->max_choice); $i++)
                                                <option value="{{$i}}" @if($option->isMandatory && $i == $option->min_choice) selected @endif>{{$i}}</option>
                                            @endfor
                                        </select> {{ $option->name }}
                                        @if($option->description)
                                            <i class="glyphicon glyphicon-info-sign right" data-toggle="tooltip" data-placement="right" title="{{ $option->description }}"></i>
                                        @endif
                                    </td>
                                    <td class="price">{{ round($option->price/100,2) }} €</td>
                                </tr>
                            @endforeach
                            </table>
                        </div>
                    </div>
                        @endif
                    @endforeach
                </fieldset>
                <a href="#" class="btn btn-info form-control" id='btnAdd'> Ajouter un billet ! </a>
                <fieldset id="basket">
                    <legend id="form">Paiment</legend>
                    <div class="form-group">
                        <label for="name" class="col-lg-2 text-right">Total</label>
                        <div class="col-lg-10" id="total_price">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-2 text-right">Moyen de paiment</label>
                        <div class="col-lg-10">
                            <select class="form-control" name="paiment">
                                @foreach($means as $mean=>$libele)
                                    <option value="{{ $mean }}">{{ $libele }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </fieldset>
                <input type="submit" class="btn btn-success form-control" value="Valider !" />
            </form>
        </div>
    </div>
@endsection

@section('sublayout-js')
    <script type="application/javascript">
        var updatePrice;
    $(function () {
        //Price

        updatePrice = function () {
           var total = 0;
            $(".options_prices").hide();
            $('select[name="price[]"] option:selected').each(function() {
                total += parseInt($(this).attr('price'));
                $(this).parentsUntil('fieldset').nextAll(".options_prices" ).hide();
                $(this).parentsUntil('fieldset').nextAll(".price_options_"+ parseInt($(this).attr('value'))).show();
            });

            $('.options_selector:visible').each(function() {
                total += parseInt($(this).attr('price') * $(this).val());
            });

            //Options

            $('#total_price').html(total.toString() + " €");

            $('select[name="price[]"]').on('change', function(){ updatePrice() });
            $('.options_selector').on('change', function(){ updatePrice() });
        };

        $('#btnAdd').click(function (event) {
            event.preventDefault();
            var num = $('.billet').length,
                    newNum = new Number(num + 1),
                    $elem = $('#billet' + num).data( "arr", [ 1 ] ),
                    newElem = $elem.clone(true).data( "arr", $.extend( [], $elem.data( "arr" ) ) ).attr('id', 'billet' + newNum);
            newElem.find('legend').text('Billet #' + newNum);
            newElem.find("input").val("");
            newElem.find(".options_prices select").each(function () {
                $(this).prop('name', $(this).prop('name').replace(/option_[0-9]+_([0-9]+)/g, 'option_'+newNum+'_$1'));
            });

            $('#billet' + num).after(newElem);
            $('#billet' + newNum).next('input').focus();
            updatePrice('.options_prices select');
        });

        updatePrice();

        // Pré-remplissage
        $('#buyer input').keyup(function(){
            $('#billet1 #'+$(this).attr('id')).val($(this).val());
        });
    });

    </script>
@endsection
