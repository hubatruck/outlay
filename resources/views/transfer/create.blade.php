@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="alert alert-warning">
                    <span class="font-weight-bolder">{{ __('Warning') }}:</span>
                    {{ __('Please be careful when selecting the destination wallet. Sending sums to wallets that have a name next to them and marked \'External\' is irreversible, as those belong to other users.') }}
                </div>

                <div class="card">
                    <div class="card-header">
                        {{ __('Create a transfer') }}
                    </div>
                    <div class="card-body">
                        <form
                            method="POST"
                            action="{{ route('transfer.data.create') }}"
                            enctype="multipart/form-data"
                            class="row"
                        >
                            @csrf

                            <div class="col-md-12">
                                <label for="description" class="form-label">
                                    {{ __('Description') }}<span class="text-danger">*</span>
                                </label>
                                <input
                                    id="description"
                                    class="form-control @error('scope') is-invalid @enderror"
                                    name="description"
                                    type="text"
                                    value="{{ old('description', '') }}"
                                    required
                                />

                                @error('description')
                                <span class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label for="amount" class="form-label">
                                    {{ __('Amount') }}<span class="text-danger">*</span>
                                </label>
                                <input
                                    id="amount"
                                    class="form-control @error('amount') is-invalid @enderror"
                                    name="amount"
                                    type="number"
                                    value="{{ old('amount', 0) }}"
                                    step="0.01"
                                    min="0.01"
                                    max="999999.99"
                                />

                                @error('amount')
                                <span class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label for="from_wallet_id" class="form-label">
                                    {{ __('Source wallet') }}<span class="text-danger">*</span></label>
                                <select
                                    id="from_wallet_id"
                                    class="form-select @error('from_wallet_id') is-invalid @enderror form-control"
                                    name="from_wallet_id"
                                    required
                                >
                                    <option selected disabled hidden value="">
                                        {{ __('Select...') }}
                                    </option>
                                    @foreach(Auth::user()->wallets as $wallet)
                                        @if($wallet->deleted_at === null)
                                            <option
                                                value="{{ $wallet->id }}"
                                                @if(($selected_wallet_id ?? '') === (string) $wallet->id
                                                    || (old('from_wallet_id') && (string) $wallet->id === old('from_wallet_id')))
                                                selected
                                                @endif
                                            >
                                                {{ $wallet->name }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                                @error('from_wallet_id')
                                <span class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label for="to_wallet_id" class="form-label">
                                    {{ __('Destination wallet') }}<span class="text-danger">*</span></label>
                                <select
                                    id="to_wallet_id"
                                    class="form-select @error('to_wallet_id') is-invalid @enderror form-control"
                                    name="to_wallet_id"
                                    required
                                >
                                    <option selected disabled hidden value="">
                                        {{ __('Select...') }}
                                    </option>
                                    @foreach(\App\Models\Wallet::withTrashed()->get() as $wallet)
                                        <option
                                            value="{{ $wallet->id }}"
                                            @if((old('to_wallet_id') && (string) $wallet->id === old('to_wallet_id')))
                                            selected
                                            @endif
                                        >
                                            {{ $wallet->name }}
                                            @if (!Auth::user()->owns($wallet))
                                                ({{ $wallet->user->name }}) - {{ __('External wallet') }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('to_wallet_id')
                                <span class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label for="transfer_date" class="form-label">
                                    {{ __('Date') }}<span class="text-danger">*</span></label>
                                <input
                                    id="transfer_date"
                                    class="form-control @error('transfer_date') is-invalid @enderror"
                                    name="transfer_date"
                                    type="date"
                                    value="{{ \Carbon\Carbon::parse(old('transfer_date', \Carbon\Carbon::now()))->format('Y-m-d') }}"
                                />

                                @error('transfer_date')
                                <span class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <div class="col-12 text-danger">{{ __('Fields marked with * are required.') }}</div>
                            <div class="col-12 mt-2">
                                <button type="submit" class="btn btn-success">
                                    {{ __('Send') }}
                                </button>
                                <a type="submit"
                                   href="{{ previousUrlOr(route('transaction.view.all')) }}"
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
