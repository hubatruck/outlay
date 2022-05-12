@php
  $steps = ['Items', 'Payment details', 'Overview'];
@endphp
<x-forms.stepper
  :action="$action"
  :submitLabel="$submitLabel"
  :previousStep="$previousStep ?? null"
  :steps="$steps"
  :complete="$complete ?? -1"
>
  {!! $slot !!}
</x-forms.stepper>
