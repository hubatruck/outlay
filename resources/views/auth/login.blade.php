@extends('layouts.app')

@section('content')
  <div class="uk-container">
    <div class="uk-flex-center uk-padding-medium" uk-grid>
      <div class="uk-width-large">
        <x-validation-errors/>

        <div class="uk-card-header">
          <h2 class="uk-text-center">{{ __('Login') }}</h2>
        </div>

        <div class="uk-card-body uk-padding-remove-top">
          <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="uk-margin">
              <label class="uk-form-label" for="name">{{ __('Username') }}</label>
              <div class="uk-form-controls">
                <div class="uk-inline uk-width-1-1">
                  <span class="uk-form-icon" uk-icon="mail"></span>
                  <input class="uk-input" type="text" name="name" id="name" value="{{ old('name') }}" required autofocus/>
                </div>
              </div>
            </div>

            <div class="uk-margin">
              <label class="uk-form-label" for="password">{{ __('Password') }}</label>
              <div class="uk-form-controls">
                <div class="uk-inline uk-width-1-1">
                  <span class="uk-form-icon" uk-icon="icon: lock"></span>
                  <input class="uk-input" type="password" name="password" id="password" required autocomplete="current-password"/>
                </div>
              </div>
            </div>

            <div class="uk-margin">
              <label>
                <input class="uk-checkbox" type="checkbox" name="remember" id="remember"
                  {{ old('remember') ? 'checked' : '' }}>
                {{ __('Remember Me') }}
              </label>
            </div>

            <div class="uk-margin">
              <button class="uk-button uk-button-primary uk-width-1-1" type="submit">
                {{ __('Login') }}
              </button>
            </div>

            @if (Route::has('password.request'))
              <div class="uk-flex uk-flex-center">
                <a href="{{ route('password.request') }}">
                  <small>{{ __('Forgot your password?') }}</small>
                </a>
                <span class="uk-margin-small-left uk-margin-small-right">|</span>
                <a href="{{ route('register') }}">
                  <small>{{ __('Sign up') }}</small>
                </a>
              </div>
            @endif
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
