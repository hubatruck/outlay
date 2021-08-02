@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                @if (session('message'))
                    <div class="alert alert-{{ session('status') }}">{{ session('message') }}</div>
                @endif
                @if (!count($wallet->transactions->toArray()))
                    <div class="alert alert-warning">
                        {{ __('No transactions for this wallet. Charts are hidden.') }}
                    </div>
                @endif
                <div class="card">
                    <div class="card-header">
                        {{ __('Wallet details for :wallet', ['wallet' => $wallet->name ?? 'UNDEFINED']) }}
                    </div>
                    <div class="card-body">
                        <div class="m-3 p-6 rounded shadow col-md-6">
                            {!! $dailyChart->container() !!}
                        </div>
                        <div class="m-3 p-6 rounded shadow col-md-6">
                            {!! $typeChart->container() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ $dailyChart->cdn() }}"></script>
    {{ $dailyChart->script() }}
    {{ $typeChart->script() }}
@endpush
