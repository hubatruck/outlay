@if (session('status'))
  @php
    $status = session('status');
    if(!is_array($status)) {
        $rawMessages = [0 => ['status' => $status]];
    } else {
        $rawMessages = $status;
    }

    $statuses = [
      'profile-information-updated'        => __('Your profile information has been updated'),
      'password-updated'                   => __('Your password has been updated'),
      'recovery-codes-generated'           => __('Two Factor recovery codes have been regenerated'),
      'two-factor-authentication-enabled'  => __('Two factor authentication has been enabled'),
      'two-factor-authentication-disabled' => __('Two factor authentication has been disabled'),
      'verification-link-sent'             => __('A new verification link has been sent to the email address you provided during registration.')
    ];

    $messages = [];
    foreach ($rawMessages as $msg){
        $content = array_key_exists($msg['status'], $statuses) ? $statuses[$msg['status']]: $msg['status'];
        $messages[] = [
            'type'=>$msg['status_type']??'success',
            'content'=>$content,
        ];
    }
  @endphp

  @foreach($messages as $msg)
    <div class="uk-alert-{{ $msg['type'] }} uk-margin-remove-top uk-margin-small-bottom" uk-alert>
      <a class="uk-alert-close" uk-close></a>
      {!!  $msg['content'] !!}
    </div>
  @endforeach
  @php
    session()->remove('status')
  @endphp
@endif
