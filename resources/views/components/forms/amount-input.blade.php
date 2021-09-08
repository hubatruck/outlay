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
    value="{{ !isset($asArray) ? old('amount', $value ?? 0) : ($value ?? 0) }}"
    step="0.01"
    min="0.01"
    max="999999.99"
  />
</div>
@error($errorField ?? 'amount')
<span class="uk-text-danger uk-text-small uk-display-block">
  <strong>{{ $message }}</strong>
</span>
@enderror
