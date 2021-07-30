@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                @if (session('message'))
                    <div class="alert alert-{{ session('status') }}">{{ session('message') }}</div>
                @endif
                <div class="card">
                    <div class="card-header">{{ __('Transactions') }}</div>
                    <div class="card-body">
                        <div class="col">
                            <a href="{{ route('transaction.view.create') }}" class="btn btn-success">{{ __('Add') }}</a>
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
                                </tr>
                                </thead>
                                <tbody>
                                @foreach(Auth::user()->transactions->sortBy('createdAt') as $transaction)
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
