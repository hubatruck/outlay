{{-- https://github.com/ArielMejiaDev/larapex-charts/blob/master/stubs/resources/views/chart/script.blade.php --}}
<script>
    (function () {
        const options_{!! $chart->id() !!} = {
            chart: {
                id: '{!! $chart->id() !!}',
                type: '{!! $chart->type() !!}',
                height: {!! $chart->height() !!},
                width: '{!! $chart->width() !!}',
                toolbar: {!! $chart->toolbar() !!},
                zoom: {!! $chart->zoom() !!},
                fontFamily: '{!! $chart->fontFamily() !!}',
                foreColor: '{!! $chart->foreColor() !!}',
                sparkline: {!! $chart->sparkline() !!},
                @if($chart->stacked())
                    stacked: {!! $chart->stacked() !!},
                @endif
            },
        plotOptions: {
        bar: {!! $chart->horizontal() !!}
    },
    colors: {!! $chart->colors() !!},
        series: {!! $chart->dataset() !!},
            dataLabels: {!! $chart->dataLabels() !!},
                @if($chart->labels())

                    labels: {!! json_encode($chart->labels(), true) !!},
                @endif
        title: {
        text: "{!! $chart->title() !!}"
    },
    subtitle: {
        text: '{!! $chart->subtitle() !!}',
            align: '{!! $chart->subtitlePosition() !!}'
    },
    xaxis: {!! $chart->xAxis() !!},
        yaxis: {
        labels: {
            show: {!! json_encode($chart->showYAxisLabels(), true) !!},
                }
    },
    @if ($chart->yAxis())
        yaxis: {!! $chart->yAxis() !!},
    @endif
        grid: {!! $chart->grid() !!},
            markers: {!! $chart->markers() !!},
                @if($chart->stroke())
                    stroke: {!! $chart->stroke() !!},
                @endif
        legend: {
        show: {!! $chart->showLegend() !!}
    },
    states: {!! json_encode($chart->states()['states']) !!}
        };

    const chart_{!! $chart->id() !!} = new ApexCharts(
        document.querySelector("#{!! $chart->id() !!}"),
        options_{!! $chart->id() !!}
    );
    chart_{!! $chart->id() !!}.render();
    }) ();
</script>