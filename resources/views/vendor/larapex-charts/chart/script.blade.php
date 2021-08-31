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
      categories: {!! $chart->xAxis() !!}
    },
    grid: {!! $chart->grid() !!},
    markers: {!! $chart->markers() !!},
    @if($chart->stroke())
    stroke: {!! $chart->stroke() !!},
    @endif
  };

  const chart_{!! $chart->id() !!} = new ApexCharts(
    document.querySelector("#{!! $chart->id() !!}"),
    options_{!! $chart->id() !!}
  );
  chart_{!! $chart->id() !!}.render();
</script>
