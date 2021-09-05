@php
  use Carbon\Carbon;
  $value = Carbon::parse(old($fieldName, $defaultValue ?? Carbon::now()))->format(globalDateFormat())
@endphp
<label for="transfer_date" class="uk-form-label">
  {{ __('Date') }}<span class="uk-text-danger">*</span>
</label>
<div class="uk-form-controls uk-inline uk-width-expand">
  <a
    class="uk-form-icon uk-form-icon-flip"
    uk-icon="home"
    onclick="clearDatePicker()"
    uk-tooltip="{{ __('Reset range') }}"
  ></a>
  <input
    id="{{ $fieldName }}"
    class="uk-input @error($fieldName) uk-form-danger @enderror"
    name="{{ $fieldName }}"
    type="date"
    value="{{ $value }}"
  />
</div>
@error($fieldName)
<span class="uk-text-danger uk-text-small">
  <strong>{{ $message }}</strong>
</span>
@enderror

@push('scripts')
  <script>
    function clearDatePicker() {
      $('#{{ $fieldName }}')[0]._flatpickr.setDate('{{ $value }}')
    }
  </script>
@endpush
