@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                @if (session('message'))
                    <div class="alert alert-{{ session('status') }}">{{ session('message') }}</div>
                @endif
                @if (!count(Auth::user()->wallets))
                    <div class="alert alert-warning">
                        {{ __('You don\'t have any wallet connected to you account, so the "transactions" feature is not available.') }}
                        <a class="alert-link" href="{{ route('wallet.view.create') }}">
                            {{ __('Create a wallet by clicking here.') }}
                        </a>
                    </div>
                @endif
                <div class="card">
                    <div class="card-header">{{ __('Transactions') }}</div>
                    <div class="card-body">
                        @if (count(Auth::user()->transactions))
                            {{ $dataTable->table(['class' => 'table  table-response table-hover dt-responsive', 'width' => '100%']) }}
                        @else
                            {{ __('You don\'t have any transactions available.') }}
                            @if(count(Auth::user()->wallets))
                                <a href="{{ route('transaction.view.create') }}">{{ __('Create') }}.</a>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{ $dataTable->scripts() }}
@endpush
