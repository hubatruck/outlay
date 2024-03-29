@extends('layouts.app')

@section('content')
  <x-page-title>{{ __('Wallets') }}</x-page-title>
  <div class="uk-card-body">
    <a href="{{ route('wallet.view.create') }}" class="uk-button uk-button-primary">{{ __('Add') }}</a>

    @if (count($wallets ?? []))
      <table class="uk-table uk-table-divider uk-table-responsive uk-table-hover">
        <thead>
        <tr class="font-weight-bold">
          <th id="actions" class="uk-table-shrink uk-table-middle">{{ __('Actions') }}</th>
          <th id="name" class="uk-table-expand">{{ __('Name') }}</th>
          <th id="balance">{{ __('Current balance') }}</th>
          <th id="publicity">{{ __('Publicity status') }}</th>
          <th id="usability">{{ __('Usability status') }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($wallets as $wallet)
          @php($cb = $wallet->currentBalance)
          <tr @if($wallet->deleted_at) style="opacity: 0.6;" @endif>
            <td>
              <a
                href="{{ route('wallet.view.details', ['id' => $wallet->id]) }}"
                class="uk-button uk-button-default uk-button-small"
              > {{ __('Details') }} </a>
            </td>
            <td>{{ $wallet->name }}</td>
            <td
              class="{{ $cb < 0 ? 'uk-text-danger' : 'uk-text-success' }}">{{ $cb }}</td>
            <td>{{ $wallet->is_public ? __('Public') : __('Private') }}</td>
            <td>{{ isset($wallet->deleted_at) ? __('Hidden') : __('Active') }}
          </tr>
        @endforeach
        </tbody>
      </table>
    @else
      <div class="uk-margin-small-top">
        {{ __('You don\'t have any wallets available.') }}
      </div>
    @endif
  </div>
@endsection
