<label for="transaction_type_id" class="uk-form-label">
  {{ __('Type') }}<span class="uk-text-danger">*</span>
</label>
<div class="uk-form-controls">
  <select
    id="transaction_type_id"
    class="uk-select @error('transaction_type_id') uk-form-danger @enderror"
    name="transaction_type_id"
    required
  >
    <option @if(!isset($transaction['transaction_type_id'])) selected @endif disabled hidden value="">
      {{ __('Select...') }}
    </option>
    @foreach(\App\Models\TransactionType::all() as $tt)
      <option
        value="{{ $tt->id }}"
        @if(isset($transaction['transaction_type_id']) && $transaction['transaction_type_id'] === $tt->id)
        selected
        @endif
        @if(old('transaction_type_id') && (string)$tt->id === old('transaction_type_id'))
        selected
        @endif
      >{{ __($tt->name) }}</option>
    @endforeach
  </select>
</div>
@error('transaction_type_id')
<span class="uk-text-danger uk-text-small">
  <strong>{{ $message }}</strong>
</span>
@enderror
