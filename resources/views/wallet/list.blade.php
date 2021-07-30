@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                @if (session('message'))
                    <div class="alert alert-{{ session('status') }}">{{ session('message') }}</div>
                @endif
                <div class="card">
                    <div class="card-header">{{ __('Wallets') }}</div>
                    <div class="card-body">
                        <div class="col">
                            <a href="{{ route('wallet.view.create') }}" class="btn btn-success">Add</a>
                        </div>
                        <hr/>

                        @if (count(Auth::user()->wallets))
                            <table class="table table-hover table-responsive">
                                <thead>
                                <tr class="font-weight-bold">
                                    <td>Actions</td>
                                    <td>Name</td>
                                    <td>Current balance</td>
                                    <td>Credit card</td>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach(Auth::user()->wallets as $wallet)
                                    <tr>
                                        <td><a class="btn btn-primary btn-sm"
                                               href="{{ url("/wallets/edit/{$wallet->id}") }}">Edit</a></td>
                                        <td>{{ $wallet->name }}</td>
                                        <td class="{{ $wallet->balance<0 ? 'table-danger' : 'table-default' }}">{{ $wallet->balance }}</td>
                                        <td>{{ $wallet->is_card ? 'yes' : 'no' }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @else
                            You don't have any wallets available.
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
