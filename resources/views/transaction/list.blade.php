@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                @if (session('message'))
                    <div class="alert alert-{{ session('status') }}">{{ session('message') }}</div>
                @endif
                @if (!Auth::user()->hasWallet())
                    <div class="alert alert-warning">
                        {{ __('You don\'t have any wallet connected to you account. Transactions feature is not available.') }}
                        <br/>
                        <a class="alert-link" href="{{ route('wallet.view.create') }}">
                            {{ __('Create a wallet by clicking here.') }}
                        </a>
                    </div>
                @elseif(!Auth::user()->hasAnyActiveWallet())
                    <div class="alert alert-warning">
                        {{ __('You don\'t have any wallet marked as active. Transaction creation is unavailable.') }}
                        <br/>
                        <a class="alert-link" href="{{ route('wallet.view.all') }}">
                            {{ __('Activate a wallet by clicking here.') }}
                        </a>
                    </div>
                @endif
                <div class="card">
                    <div class="card-header">{{ __('Transactions') }}</div>
                    <div class="card-body">
                        @if (Auth::user()->hasTransactions())
                            {{ $dataTable->table(['class' => 'table table-response table-hover dt-responsive', 'width' => '100%']) }}
                        @else
                            {{ __('You don\'t have any transactions available.') }}
                            @if(Auth::user()->hasAnyActiveWallet())
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
