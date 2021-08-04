@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        {{ isset($wallet) ? __(':action wallet', ['action'=> __('Edit')]) : __(':action wallet', ['action' => __('Create')]) }}
                    </div>
                    <div class="card-body">
                        <form
                            method="POST"
                            action="{{ isset($wallet) ? route('wallet.data.update', ['id' => $wallet->id]) : route('wallet.data.create') }}"
                            enctype="multipart/form-data"
                            class="row"
                        >
                            @csrf

                            <div class="col-md-12">
                                <label for="name" class="form-label">{{ __('Name') }}<span class="text-danger">*</span></label>
                                <input
                                    id="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    name="name"
                                    type="text"
                                    value="{{ old('name', isset($wallet) ? $wallet->name : '') }}"
                                    required
                                />

                                @error('name')
                                <span class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label for="notes" class="form-label">{{ __('Notes') }}</label>
                                <textarea
                                    id="notes"
                                    class="form-control @error('notes') is-invalid @enderror"
                                    name="notes"
                                    placeholder="{{ __('This field is optional.') }}"
                                >{{ old('notes', isset($wallet) ? $wallet->notes : '') }}</textarea>

                                @error('notes')
                                <span class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-md-12">
                                <label for="balance" class="form-label">{{ __('Balance') }}</label>
                                <input
                                    id="balance"
                                    class="form-control @error('balance') is-invalid @enderror"
                                    name="balance"
                                    type="number"
                                    value="{{ old('balance', isset($wallet) ? $wallet->balance : 0) }}"
                                    step="0.01"
                                />

                                @error('balance')
                                <span class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-md-12">
                                <div class="form-check">
                                    <input
                                        id="is_card"
                                        class="form-check-input"
                                        name="is_card"
                                        type="checkbox"
                                        {{ old('is_card', isset($wallet) && $wallet->is_card ? 'checked' : '') }}
                                    >
                                    <label class="form-check-label" for="is_card">
                                        {{ __('Wallet represents credit card') }}
                                    </label>
                                </div>
                            </div>

                            <div class="col-12 text-danger">{{ __('Fields marked with * are required.') }}</div>
                            <div class="col-12 mt-2">
                                <button type="submit" class="btn btn-success">
                                    {{ isset($wallet) ? __('Save') : __('Create') }}
                                </button>
                                <a
                                    type="submit"
                                    href="{{ url()->current() !== url()->previous() ? url()->previous() : route('wallet.view.all')}}"
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
