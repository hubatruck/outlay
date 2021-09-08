<label for="amount" class="uk-form-label">
  {{ __('Amount') }}
  @unless (isset($notRequired))
    <span class="uk-text-danger">*</span>
  @endunless
</label>
<div class="uk-form-controls">
  <input
    @unless (isset($asArray))
    id="amount"
    @endunless
    class="uk-input @error('amount') uk-form-danger @enderror"
    name="{{ isset($asArray) ? 'amount[]' : 'amount' }}"
    type="number"
    value="{{ old('amount', $value ?? 0) }}"
    step="0.01"
    min="0.01"
    max="999999.99"
  />
</div>
@error('amount')
<span class="uk-text-danger uk-text-small">
  <strong>{{ $message }}</strong>
</span>
@enderror
