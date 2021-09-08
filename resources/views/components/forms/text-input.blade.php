<label for="scope" class="uk-form-label">
  {{ $label }}<span class="uk-text-danger">*</span>
</label>
<div class="uk-form-controls">
  <input
    @unless ($errorField)
    id="{{ $fieldName }}"
    @endunless
    class="uk-input @error('scope')uk-form-danger @enderror"
    name="{{ $fieldName }}"
    type="text"
    value="{{ old($fieldName, $value ?? '') }}"
    required
  />
</div>
@error($errorField ?? $fieldName)
<span class="uk-text-danger uk-text-small uk-display-block">
  <strong>{{ $message }}</strong>
</span>
@enderror
