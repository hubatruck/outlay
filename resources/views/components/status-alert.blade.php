@php
  if (session('status')) {
      $status = session('status');
      $statuses = [
          'profile-information-updated' => __('Your profile information has been updated'),
          'password-updated' => __('Your password has been updated'),
          'recovery-codes-generated' => __('Two Factor recovery codes have been regenerated'),
          'two-factor-authentication-enabled' => __('Two factor authentication has been enabled'),
          'two-factor-authentication-disabled' => __('Two factor authentication has been disabled'),
          'verification-link-sent' => __('A new verification link has been sent to the email address you provided during registration.'),
      ];
      $content = array_key_exists($status, $statuses) ? $statuses[$status] : $status;
      addSessionMsg(['content' => $content, 'type' => 'success']);
  }
@endphp

@if(session('messages'))
  @php
    $messages = session('messages')
  @endphp

  @foreach($messages as $msg)
    @if(isset($msg['content']))
      <div class="uk-alert-{{ $msg['type'] ?? 'default' }} uk-margin-remove-top uk-margin-small-bottom" uk-alert>
        <a class="uk-alert-close" uk-close></a>
        {!! $msg['content'] !!}
      </div>
    @endif
  @endforeach
@endif
