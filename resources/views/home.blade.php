@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Dashboard') }}</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <div class="card-group">
                            <div class="card mb-3" style="width: 18rem;">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __('Transactions')}}</h5>
                                    <p class="card-text">{{ __('View your transactions.') }}</p>
                                    <a href="{{ route('transaction.view.all')}}"
                                       class="btn btn-outline-primary">{{ __('Visit') }}</a>
                                </div>
                            </div>

                            <div class="card mb-3" style="width: 18rem;">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __('Wallets')}}</h5>
                                    <p class="card-text">{{ __('View wallets linked to your account.') }}</p>
                                    <a href="{{ route('wallet.view.all')}}"
                                       class="btn btn-outline-primary">{{ __('Visit') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
