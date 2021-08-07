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
                                <caption>Table status</caption>
                                <thead>
                                <tr class="font-weight-bold">
                                    <th scope="col">{{ __('Actions') }}</th>
                                    <th scope="col">{{ __('Name') }}</th>
                                    <th scope="col">{{ __('Current balance') }}</th>
                                    <th scope="col">{{ __('Credit card') }}</th>
                                    <th scope="col">{{ __('Status') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($wallets as $wallet)
                                    <tr @if($wallet->deleted_at) style="opacity: 0.6;" @endif>
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
                                        <td>{{ isset($wallet->deleted_at) ? __('HIDDEN') : __('Active') }}
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
