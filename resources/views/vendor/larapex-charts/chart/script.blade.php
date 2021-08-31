<script>
  const options_{!! $chart->id() !!} = {
    chart: {
      type: '{!! $chart->type() !!}',
      height: {!! $chart->height() !!},
      width: '{!! $chart->width() !!}',
      toolbar: {!! $chart->toolbar() !!},
      zoom: {!! $chart->zoom() !!},
      fontFamily: '{!! $chart->fontFamily() !!}',
      foreColor: '{!! $chart->foreColor() !!}'
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
    xaxis: {
      categories: {!! $chart->xAxis() !!},
      @if (in_array($chart->type(), ['area', 'line', 'bar']) && !json_decode($chart->horizontal(), false, 512, JSON_THROW_ON_ERROR)->horizontal)
      type: 'datetime',
      labels: {
        formatter: function (value, timestamp) {
          const date = new Date(timestamp);
          return date.toLocaleDateString('{{ config('app.locale') }}', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
          });
        },
      },
      @endif
    },
    grid: {!! $chart->grid() !!},
    markers: {!! $chart->markers() !!},
    @if($chart->stroke())
    stroke: {!! $chart->stroke() !!},
    @else
    stroke: {curve: 'smooth'},
    @endif
  };

  const chart_{!! $chart->id() !!} = new ApexCharts(
    document.querySelector("#{!! $chart->id() !!}"),
    options_{!! $chart->id() !!}
  );
  chart_{!! $chart->id() !!}.render();
</script>
