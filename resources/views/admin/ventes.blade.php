@extends('layouts.dashboard')

@section('title')
    Ventes
@endsection

@section('smalltitle')
    Récapitulatif
@endsection

@section('content')
    <div class="box box-default">

        <div class="box-header with-border">
            <h3 class="box-title">Graphique</h3>
        </div>
        <div class="box-body">
            <canvas id="sellChart" width="400" height="250"></canvas>
        </div>
    </div>

    <div class="box box-default">

        <div class="box-header with-border">
            <h3 class="box-title">Stats Tarifs</h3>
        </div>
        <div class="box-body">
            <?php $total =0; ?>
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>Tarifs</th>
                        <th>Vendu</th>
                        <th>Restant</th>
                        <th>Validés</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($prices as $price)
                            <tr class="vert-align">
                                <td>
                                    <strong>{{ $price->name }}</strong>
                                </td>
                                <?php $total += $price->billets->count(); ?>
                                <td>{{ $price->billets->count() }} </td>
                                <td> @if($price->max == 0)Illimité @else {{ $price->max - $price->billetSold(true) }} @endif</td>
                                <td>{{ $price->billets->count() - $price->billets->where('validated_at', null)->count() }}</td>
                            </tr>
                    @endforeach
                    <tr class="vert-align">
                        <td>
                            Totaux
                        </td>
                        <td>{{ $total }} </td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr class="vert-align">
                        <td>
                            <strong>Dons</strong>
                        </td>
                        <td>{{ $dons/100 }} €</td>
                    </tr>
                    </tbody>
                </table>
        </div>
    </div>

    <div class="box box-default">

        <div class="box-header with-border">
            <h3 class="box-title">Stats Options</h3>
        </div>
        <div class="box-body">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>Option</th>
                    <th>Nombres</th>
                    <th>Disponible</th>
                </tr>
                </thead>
                <tbody>
                @foreach($options as $option)
                    <tr class="vert-align">
                        <td>
                            <strong>{{ $option->name }}</strong>
                        </td>
                        <td>{{ $option->billets()->withPivot('qty')->get()->sum('pivot.qty') }}</td>
                        <td>@if($option->max_order == 0) Illimité @else {{ ($option->max_order - $option->billets()->withPivot('qty')->get()->sum('pivot.qty')) }}@endif</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
@section('sublayout-js')
    @parent
    <script src="{{ asset('js/Chart.min.js') }}"></script>
    <script type="text/javascript">
        var ChartOptions = {
                //Boolean - If we should show the scale at all
                showScale: true,
                //Boolean - Whether grid lines are shown across the chart
                scaleShowGridLines: false,
                //String - Colour of the grid lines
                scaleGridLineColor: "rgba(0,0,0,.05)",
                //Number - Width of the grid lines
                scaleGridLineWidth: 1,
                //Boolean - Whether to show horizontal lines (except X axis)
                scaleShowHorizontalLines: true,
                //Boolean - Whether to show vertical lines (except Y axis)
                scaleShowVerticalLines: true,
                //Boolean - Whether the line is curved between points
                bezierCurve: true,
                //Number - Tension of the bezier curve between points
                bezierCurveTension: 0.3,
                //Boolean - Whether to show a dot for each point
                pointDot: false,
                //Number - Radius of each point dot in pixels
                pointDotRadius: 4,
                //Number - Pixel width of point dot stroke
                pointDotStrokeWidth: 1,
                //Number - amount extra to add to the radius to cater for hit detection outside the drawn point
                pointHitDetectionRadius: 20,
                //Boolean - Whether to show a stroke for datasets
                datasetStroke: true,
                //Number - Pixel width of dataset stroke
                datasetStrokeWidth: 2,
                //Boolean - Whether to fill the dataset with a color
                datasetFill: false,
                //String - A legend template
                legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].lineColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>",
                  //Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
                  maintainAspectRatio: true,
                  //Boolean - whether to make the chart responsive to window resizing
                  responsive: true
                };
        var ctx = document.getElementById("sellChart");
        var sellChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [@foreach($dates_interval as $day) '{{$day->format("d-m-Y")}}', @endforeach],
                datasets: [
                    @foreach($stats->pluck('name')->unique() as $price_name)
                        <?php $stat_prices = $stats->where('name',$price_name);
                            $rgbColor = array();
                            foreach(array('r', 'g', 'b') as $color){
                                $rgbColor[$color] = mt_rand(0, 255);
                            }
                        ?>
                    {
                    label: '{{ $price_name }}',
                    data: [ <?php
                            foreach ($dates_interval as $day)
                                {
                                    $stat = $stat_prices->where('day',$day->format('Y-m-d'));
                                    if($stat->count() > 0)
                                        echo $stat->first()->count;
                                    else echo 0;
                                    echo ',';
                                }
                            ?>
                        ],
                    backgroundColor: 'rgba({{ implode(",", $rgbColor) }}, 0.2)',
                    borderColor: 'rgba({{ implode(",", $rgbColor) }},1)',
                    borderWidth: 1
                },
                @endforeach
            {
                label: 'Total',
                data: [ <?php
                    foreach ($dates_interval as $day)
                    {
                        $stat = $stats->where('day',$day->format('Y-m-d'));
                        if($stat->count() > 0)
                            echo $stat->sum('count');
                        else echo 0;
                        echo ',';
                    }
                    ?>
                ],
                backgroundColor: 'rgba(0,0,0, 0.2)',
                borderColor: 'rgba(0,0,0,1)',
                borderWidth: 1
            },
                ]
            },
            options: ChartOptions
        });
    </script>
@endsection
