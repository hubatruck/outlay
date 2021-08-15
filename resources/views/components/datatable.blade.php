@extends('layouts.app')

@section('content')
  <x-page-title>{{ $title }}</x-page-title>
  <div class="uk-card-body">
    @if($shouldRenderTable)
      {!! $dataTable->table(['class' => 'uk-table uk-table-response uk-table-divider uk-table-hover dt-responsive uk-margin-remove', 'width' => '100%'], true) !!}
    @else
      {{ __('Nothing here...') }}
      @if(Auth::user()->hasAnyActiveWallet())
        <a href="{{ $createLink }}">{{ __('Create') }}.</a>
      @endif
    @endif
  </div>
@endsection

@push('scripts')
  {{ $dataTable->scripts() }}
@endpush
