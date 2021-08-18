<nav class="uk-navbar-container" uk-navbar="mode: click">
  <div class="uk-navbar-left">
    @auth
      <span class="uk-margin-small-left" uk-icon="menu" uk-toggle="target: #side-menu"></span>
    @endauth
    <a class="uk-navbar-item uk-logo" href="{{ route('home') }}">
      {{ config('app.name', 'Laravel') }}
    </a>
  </div>

  <div class="uk-navbar-right">
    <ul class="uk-navbar-nav">
      <li>
        <a href="#"><span uk-icon="grid"></span></a>
        <div class="uk-navbar-dropdown uk-width-medium">
          <ul class="uk-nav uk-navbar-dropdown-nav">
            <!-- Authentication Links -->
            @guest
              <li>
                <a href="{{ route('login') }}">
                  <span uk-icon="sign-in" class="uk-margin-small-right"></span>
                  {{ __('Login') }}
                </a>
              </li>

              @if (Route::has('register'))
                <li>
                  <a href="{{ route('register') }}">
                    <span uk-icon="user" class="uk-margin-small-right"></span>
                    {{ __('Register') }}
                  </a>
                </li>
              @endif
            @else
              <li class="uk-nav-header">
                {{ __('Hi') }}, {{ Auth::user()->name }}
              </li>

              <li class="uk-nav-divider"></li>

              <li>
                <a href="/user/profile">
                  <span uk-icon="user" class="uk-margin-small-right"></span>
                  {{ __('Profile') }}
                </a>
              </li>

              <li>
                <a href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                  <span uk-icon="sign-out" class="uk-margin-small-right"></span>
                  {{ __('Logout') }}
                </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" uk-hidden>
                  @csrf
                </form>
              </li>
            @endguest
          </ul>
        </div>
      </li>
    </ul>
  </div>
</nav>
