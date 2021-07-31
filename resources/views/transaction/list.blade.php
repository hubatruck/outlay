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
                        <a class="alert-link" href="{{ route('wallet.view.create') }}">{{ __('Create a wallet by clicking here.') }}</a>
                    </div>
                @endif
                <div class="card">
                    <div class="card-header">{{ __('Transactions') }}</div>
                    <div class="card-body">
                        <div class="col">
                            <a
                                @if (count(Auth::user()->wallets))
                                href="{{ route('transaction.view.create') }}"
                                class="btn btn-success"
                                @else
                                disabled
                                class="btn btn-success disabled"
                                @endif
                            >{{ __('Add') }}</a>
                        </div>
                        <hr/>

                        @if (count(Auth::user()->transactions))
                            <table class="table table-hover table-responsive">
                                <thead>
                                <tr class="font-weight-bold">
                                    <td>{{ __('Actions') }}</td>
                                    <td>{{ __('Scope') }}</td>
                                    <td>{{ __('Amount') }}</td>
                                    <td>{{ __('Type') }}</td>
                                    <td>{{ __('Wallet') }}</td>
                                    <td>{{ __('Date') }}</td>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach(Auth::user()->transactions->sortByDesc('created_at') as $transaction)
                                    <tr>
                                        <td>
                                            <a class="btn btn-primary btn-sm"
                                               href="{{ url("/transactions/edit/{$transaction->id}") }}">
                                                {{ __('Edit') }}
                                            </a>
                                        </td>
                                        <td>{{ $transaction->scope }}</td>
                                        <td>{{ $transaction->amount }}</td>
                                        <td>{{ __($transaction->transactionType->name)  }}</td>
                                        <td>{{ $transaction->wallet->name }}</td>
                                        <td>{{ $transaction->created_at }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @else
                            {{ __('You don\'t have any transactions available.') }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
