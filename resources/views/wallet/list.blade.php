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
                            <a href="{{ route('wallet.view.create') }}" class="btn btn-success">{{ __('Add') }}</a>
                        </div>
                        <hr/>
                        @if (count($wallets ?? []))
                            <table class="table table-hover table-responsive">
                                <thead>
                                <tr class="font-weight-bold">
                                    <td>{{ __('Actions') }}</td>
                                    <td>{{ __('Name') }}</td>
                                    <td>{{ __('Current balance') }}</td>
                                    <td>{{ __('Credit card') }}</td>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($wallets as $wallet)
                                    <tr>
                                        <td>
                                            <a
                                                href="{{ route('wallet.view.details', ['id' => $wallet->id]) }}"
                                                class="btn btn-primary btn-sm"
                                            >
                                                {{ __('Details') }}
                                            </a>
                                        </td>
                                        <td>{{ $wallet->name }}</td>
                                        <td class="{{ $wallet->balance < 0 ? 'table-danger' : 'table-default' }}">{{ $wallet->balance }}</td>
                                        <td>{{ $wallet->is_card ? __('yes') : __('no') }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @else
                            {{ __('You don\'t have any wallets available.') }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
