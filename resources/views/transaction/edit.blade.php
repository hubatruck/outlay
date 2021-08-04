@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        {{ isset($transaction) ? __(':action transaction', ['action'=> __('Edit')]) : __(':action transaction', ['action' => __('Create')]) }}
                    </div>
                    <div class="card-body">
                        <form
                            method="POST"
                            action="{{ isset($transaction) ? route('transaction.data.update', ['id' => $transaction->id]) : route('transaction.data.create') }}"
                            enctype="multipart/form-data"
                            class="row"
                        >
                            @csrf

                            <div class="col-md-12">
                                <label for="scope" class="form-label">
                                    {{ __('Scope') }}<span class="text-danger">*</span>
                                </label>
                                <input
                                    id="scope"
                                    class="form-control @error('scope') is-invalid @enderror"
                                    name="scope"
                                    type="text"
                                    value="{{ old('scope', $transaction->scope ?? '') }}"
                                    required
                                />

                                @error('scope')
                                <span class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label for="amount" class="form-label">{{ __('Amount') }}</label>
                                <input
                                    id="amount"
                                    class="form-control @error('amount') is-invalid @enderror"
                                    name="amount"
                                    type="number"
                                    value="{{ old('amount', $transaction->amount ?? 0) }}"
                                    step="0.01"
                                />

                                @error('amount')
                                <span class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label for="wallet_id" class="form-label">
                                    {{ __('Wallet') }}<span class="text-danger">*</span></label>
                                <select
                                    id="wallet_id"
                                    class="form-select @error('wallet_id') is-invalid @enderror form-control"
                                    name="wallet_id"
                                    @if (Auth::user()->hasAnyActiveWallet())
                                    required
                                    @else
                                    disabled
                                    @endif
                                >
                                    <option @if(!isset($transaction)) selected @endif disabled hidden value="">
                                        {{ __('Select...') }}
                                    </option>
                                    @foreach(Auth::user()->wallets as $wallet)
                                        @if ($wallet->deleted_at === null
                                            || (isset($transaction) && $transaction->wallet_id === $wallet->id)
                                            || (old('wallet_id')&&(string)$wallet->id===old('wallet_id')))
                                            <option
                                                value="{{ $wallet->id }}"
                                                @if((isset($transaction) && $wallet->id === $transaction->wallet_id)
                                                    || ($selected_wallet_id ?? '') === (string) $wallet->id
                                                    || (old('wallet_id') && (string) $wallet->id === old('wallet_id')))
                                                selected
                                                @endif
                                                @if ($wallet->deleted_at !== null)
                                                disabled
                                                @endif
                                            >
                                                {{ $wallet->name }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                                @if (!Auth::user()->hasAnyActiveWallet())
                                    <span class="alert-info">
                                        {{ __('No active wallets; this field cannot be changed.') }}
                                    </span>
                                @endif
                                @error('wallet_id')
                                <span class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label for="transaction_type_id" class="form-label">
                                    {{ __('Type') }}<span class="text-danger">*</span>
                                </label>
                                <select
                                    id="transaction_type_id"
                                    class="form-select @error('transaction_type_id') is-invalid @enderror form-control"
                                    name="transaction_type_id"
                                    required
                                >
                                    <option @if(!isset($transaction)) selected @endif disabled hidden value="">
                                        {{ __('Select...') }}
                                    </option>
                                    @foreach(\App\Models\TransactionType::all() as $tt)
                                        <option
                                            value="{{ $tt->id }}"
                                            @if(isset($transaction) && $transaction->transactionType->id === $tt->id)
                                            selected
                                            @endif
                                            @if(old('transaction_type_id') && (string)$tt->id === old('transaction_type_id'))
                                            selected
                                            @endif
                                        >{{ __($tt->name) }}</option>
                                    @endforeach
                                </select>

                                @error('transaction_type_id')
                                <span class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label for="transaction_date" class="form-label">
                                    {{ __('Date') }}<span class="text-danger">*</span></label>
                                <input
                                    id="transaction_date"
                                    class="form-control @error('transaction_date') is-invalid @enderror"
                                    name="transaction_date"
                                    type="date"
                                    value="{{ \Carbon\Carbon::parse(old('transaction_date',$transaction->transaction_date ?? now()))->format('Y-m-d') }}"
                                />

                                @error('transaction_date')
                                <span class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <div class="col-12 text-danger">{{ __('Fields marked with * are required.') }}</div>
                            <div class="col-12 mt-2">
                                <button type="submit" class="btn btn-success">
                                    {{ isset($transaction) ? __('Save') : __('Create') }}
                                </button>
                                <a type="submit"
                                   href="{{ url()->current() !== url()->previous() ? url()->previous() : route('transaction.view.all')}}"
                                   class="btn btn-outline-danger"
                                >
                                    {{ __('Cancel') }}
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
