@extends('layouts.app')

@section('content')
  <x-page-title>{{ __('Transaction creator') }} - {{ __('Overview') }}</x-page-title>
  <div class="uk-card-body">
    Coming soon...<br>
    <pre>{{ print_r($transaction ?? [], true) }}</pre>
  </div>
@endsection
