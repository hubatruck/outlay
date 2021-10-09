<div class="uk-grid transaction-item">
  <div class="uk-width-2-3@s">
    <x-forms.text-input
      fieldName="scope[]"
      :label="__('Scope')"
      :value="isset($transaction['scope']) ? $transaction['scope'][$index] ?? '' : ''"
      :errorField="'scope.' . ($index ?? '0')"
    />
  </div>

  <div class="uk-width-1-3@s uk-inline">
    <x-forms.amount-input
      asArray="true"
      :value="isset($transaction['amount']) ? $transaction['amount'][$index] ?? '' : ''"
      :errorField="'amount.' . ($index ?? '0')"
    />

    @if(isset($index) && $index>0)
      <span class="remove-row" uk-icon="trash">{{ __('Delete this row') }}</span>
    @else
      <span class="remove-row"></span>
    @endif
  </div>
</div>
