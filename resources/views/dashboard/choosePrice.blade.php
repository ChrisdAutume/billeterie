@extends('layouts.dashboard')

@section('title')
    Billet
@endsection

@section('smalltitle')
    Choix du tarif
@endsection

@section('content')
    <div class="callout callout-info">
        <h4>Vous n'avez pas accès au tarif qui vous correspond ?</h4>
        <p>Envoyez un mail auprés de l'équipe organisatrice <a href="mailto:{{ config('billeterie.contact') }}">{{ config('billeterie.contact') }}</a> .</p>
    </div>

    <div class="box box-default">

        <div class="box-header with-border">
            <h3 class="box-title">Choix du tarif</h3>
        </div>
        <div class="box-body">
            <form class="form-horizontal" action="{{ route('processNewItem') }}" method="post" enctype="multipart/form-data">
                {{ csrf_field() }}
                    <div class="form-group">
                        <label for="name" class="col-lg-2 text-right">Nom</label>
                        <div class="col-lg-10">
                            {{ $billet->name }}
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="surname" class="col-lg-2 text-right">Prénom</label>
                        <div class="col-lg-10">
                            {{ $billet->surname }}
                        </div>
                    </div>


                    <div class="form-group">
                        <label for="mail" class="col-lg-2 text-right">Mail</label>
                        <div class="col-lg-10">
                                {{ $billet->mail }}
                        </div>
                    </div>


                <table class="table table-hover" id="basket">
                    <thead>
                    <tr>
                        <th></th>
                        <th></th>
                        <th>Prix</th>
                        <th>Choix</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($prices as $price)
                        @if($price->canBeBuy($billet->mail))
                    <tr class="vert-align">
                        <td>
                            <strong>{{ $price->name }}</strong>
                            <p>
                                {{ $price->description }}
                            </p>
                        </td>
                        <td></td>
                        <td class="price">{{ round($price->price/100,2) }} €</td>
                        <td><input type="radio" name="price_type" value="{{ $price->id }}"></td>
                    </tr>

                    @foreach($price->optionsSellable()->where('isMandatory', false)->orderBy('name')->get() as $option)
                    <tr class="vert-align options price_{{ $price->id }}">
                        <td>
                        </td>
                        <td>
                            <select name="option_{{ $price->id }}_{{ $option->id }}" @if($option->available() == 0) disabled @endif>
                                @for($i=0; $i <= (($option->max_choice>$option->available())?$option->available():$option->max_choice); $i++)
                                    <option value="{{$i}}">{{$i}}</option>
                                @endfor
                            </select> {{ $option->name }}
                            @if($option->description)
                            <i class="glyphicon glyphicon-info-sign right" data-toggle="tooltip" data-placement="right" title="{{ $option->description }}"></i>
                            @endif
                        </td>
                        <td class="price">{{ round($option->price/100,2) }} €</td>
                        <td></td>
                    </tr>
                    @endforeach
                            @else
                            <tr class="vert-align">
                                <td>
                                    <i>{{ $price->name }}</i>
                                    <p>
                                        <i>{{ $price->description }}</i>
                                    </p>
                                </td>
                                <td></td>
                                <td class="price">{{ round($price->price/100,2) }} €</td>
                                <td><input type="radio" name="price_type" disabled value="{{ $price->id }}"></td>
                            </tr>
                    @endif
                    @endforeach
                    </tbody>
                </table>
                <input type="submit" class="btn btn-success form-control" value="Suivant !" />
            </form>
        </div>
    </div>
@endsection
@section('sublayout-js')
    <script>
        $(document).ready(function () {
            $('.options').fadeOut();
            $('input[type=radio][name=price_type]').change(function () {
                var price_id = $('input[type=radio][name=price_type]:checked').attr('value');
                $('.options').fadeOut();
                $('.price_'+price_id).fadeIn();
            });
        });
    </script>
@endsection
