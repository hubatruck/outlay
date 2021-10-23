<label for="wallet_id" class="uk-form-label">
    {{ __('Wallet') }}<span class="uk-text-danger">*</span></label>
<div class="uk-form-controls">
    <select
        id="wallet_id"
        class="uk-select @error('wallet_id')uk-form-danger @enderror"
        name="wallet_id"
        @if (Auth::user()->hasAnyActiveWallet())
        required
        @else
        disabled
        @endif
    >
        @if(!isset($transaction['wallet_id']))
            <option selected disabled hidden value="">
                {{ __('Select...') }}
            </option>
        @endif
        @foreach(Auth::user()->wallets as $wallet)
            @if ($isWalletUsable($wallet))
                <option
                    value="{{ $wallet->id }}"
                    @if($shouldSetAsSelected($wallet))
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
        <span class="uk-alert-primary uk-padding-remove">
      {{ __('No active wallets; this field cannot be changed.') }}
    </span>
    @endif
</div>
@error('wallet_id')
<span class="uk-text-danger uk-text-small">
  <strong>{{ $message }}</strong>
</span>
@enderror
