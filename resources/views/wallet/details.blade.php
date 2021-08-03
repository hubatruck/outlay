@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                @if (session('message'))
                    <div class="alert alert-{{ session('status') }}">{{ session('message') }}</div>
                @endif
                @if ($wallet->deleted_at)
                    <div class="alert alert-info">
                        <span class="font-weight-bolder">{{ __('Note') }}</span>:
                        {{ __('This wallet is marked as hidden. Cannot be used for new transactions until reactivated.') }}
                    </div>
                @endif
                @if (!count($wallet->transactions->toArray()))
                    <div class="alert alert-info">
                        <span class="font-weight-bolder">{{ __('Note') }}</span>:
                        {{ __('No transactions for this wallet. Charts are hidden.') }}
                    </div>
                @endif
                <div class="card">
                    <div class="card-header">
                        <h4>{{ __('Wallet details for :wallet', ['wallet' => $wallet->name ?? 'ERR:UNDEFINED']) }}</h4>
                        <div class="card-body">
                            <div class="card mb-2">
                                <div class="card-header">
                                    {{ __('Manage wallet') }}
                                </div>
                                <div class="card-body">
                                    <a
                                        class="btn btn-outline-success"
                                        href="{{ route('wallet.view.update', ['id' => $wallet->id]) }}"
                                    >
                                        {{ __('Edit') }}
                                    </a>
                                    <a
                                        class="btn btn-outline-danger"
                                        href="{{ route('wallet.manage.toggle_hidden', ['id' => $wallet->id]) }}"
                                    >
                                        {{ $wallet->deleted_at ? __('Reactivate') : __('Hide') }}
                                    </a>
                                    @if(!count($wallet->transactions))
                                        <a
                                            class="btn btn-danger"
                                            href="{{ route('wallet.manage.delete', ['id' => $wallet->id]) }}"
                                        >
                                            {{ __('Delete') }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                            @if (count($wallet->transactions))
                                <div class="card">
                                    <div class="card-header">
                                        <h5>{{  __('Transaction charts for :month', ['month' => __(date('F'))]) }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="col-12">
                                            <div class="row">
                                                <div class=" col-12 pt-3 mb-1 rounded shadow">
                                                    {!! $dailyChart->container() !!}
                                                </div>
                                                <div class="col-md-6 p-3 rounded shadow">
                                                    {{$typeChart->container() }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ @asset('vendor/larapex-charts/apexcharts.js') }}"></script>
    {{ $dailyChart->script() }}
    {{ $typeChart->script() }}
@endpush
