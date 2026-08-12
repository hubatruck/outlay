@php
  use App\Models\Wallet;
  $addOwnerName ??= false;
  $nameSelect = fn(Wallet $wallet) => ($addOwnerName) ? walletNameWithOwner($wallet, true) : $wallet->name
@endphp
<label for="{{ $fieldName }}" class="uk-form-label">
  {{ $label }}<span class="uk-text-danger">*</span>
</label>
<div class="uk-form-controls">
  <select id="{{ $fieldName }}" class="uk-select @error($fieldName)uk-form-danger @enderror" name="{{ $fieldName }}"
    required>
    <option selected disabled hidden value="">
      {{ __('Select...') }}
    </option>
    @foreach($wallets as $wallet)
      @if($wallet->deleted_at === null)
        <option value="{{ $wallet->id }}" @if(
          $selectedWalletID === (string) $wallet->id
          || (old($fieldName) && (string) $wallet->id === old($fieldName))
        ) selected @endif>
          {{ $nameSelect($wallet) }}
        </option>
      @endif
    @endforeach
  </select>
</div>
@error($fieldName)
  <span class="uk-text-danger uk-text-small">
    <strong>{{ $message }}</strong>
  </span>
@enderror
{{-- TODO: custom label format function for destination wallet --}}
{{-- use a global helper to make things easier --}}