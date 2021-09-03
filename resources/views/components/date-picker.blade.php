<label for="transfer_date" class="uk-form-label">
  {{ __('Date') }}<span class="uk-text-danger">*</span>
</label>
<div class="uk-form-controls">
  <input
    id="{{ $fieldName }}"
    class="uk-input @error($fieldName) uk-form-danger @enderror"
    name="{{ $fieldName }}"
    type="date"
    value="{{ \Carbon\Carbon::parse(old($fieldName, $defaultValue ?? \Carbon\Carbon::now()))->format(globalDateFormat()) }}"
  />
</div>
@error($fieldName)
<span class="uk-text-danger uk-text-small">
          <strong>{{ $message }}</strong>
        </span>
@enderror
