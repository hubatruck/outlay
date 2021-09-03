<label for="scope" class="uk-form-label">
  {{ $label }}<span class="uk-text-danger">*</span>
</label>
<div class="uk-form-controls">
  <input
    id="{{ $fieldName }}"
    class="uk-input @error('scope')uk-form-danger @enderror"
    name="{{ $fieldName }}"
    type="text"
    value="{{ old($fieldName, $value ?? '') }}"
    required
  />
</div>
@error($fieldName)
<span class="uk-text-danger uk-text-small">
  <strong>{{ $message }}</strong>
</span>
@enderror
