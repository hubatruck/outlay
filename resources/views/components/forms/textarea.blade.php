<label for="{{ $fieldName }}" class="uk-form-label">{{ $label }}</label>
<div class="form-controls">
  <textarea
    id="{{ $fieldName }}"
    class="uk-textarea @error('notes')uk-form-danger @enderror"
    name="{{ $fieldName }}"
    placeholder="{{ __('This field is optional.') }}"
  >{{ old($fieldName, $value ?? '') }}</textarea>
</div>
@error($fieldName)
<span class="uk-text-danger uk-text-small">
  <strong>{{ $message }}</strong>
</span>
@enderror
