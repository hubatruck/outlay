<!DOCTYPE html>
<html lang="en">
<head>
  <title>Print Table</title>
  <meta charset="UTF-8">
  <meta name=description content="">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="{{ mix('css/print.css') }}"/>
  <style>
    body {
      margin: 20px
    }
  </style>
</head>
<body>
<table class="uk-table uk-table-striped">
  @foreach($data as $row)
    @if ($loop->first)
      <tr>
        @foreach($row as $key => $value)
          <th>{!! $key !!}</th>
        @endforeach
      </tr>
    @endif
    <tr>
      @foreach($row as $key => $value)
        @if(is_string($value) || is_numeric($value))
          <td>{!! $value !!}</td>
        @else
          <td></td>
        @endif
      @endforeach
    </tr>
  @endforeach
</table>
<script>
  window.onload = () => {
    window.print();
    setTimeout(() => {
      window.close()
    }, 0);
  }
</script>
</body>
</html>
