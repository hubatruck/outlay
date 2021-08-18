<div id="side-menu" uk-offcanvas="mode: push">
  <div class="uk-offcanvas-bar">
    <button class="uk-offcanvas-close" type="button" uk-close></button>
    <ul class="uk-nav uk-nav-primary uk-nav-default uk-navbar-dropdown-nav">
      <li class="uk-parent">
        <a href="{{ route('home') }}">{{ __('Dashboard') }}</a>
      </li>
      <li class="uk-parent">
        <a class="uk-active" href="{{ route('transaction.view.all') }}">{{ __('Transactions') }}</a>
        <ul class="uk-nav-sub">
          <li><a href="{{ route('transaction.view.all') }}">{{ __('View all') }}</a></li>
          <li><a href="{{ route('transaction.view.create') }}">{{ __('Create') }}</a></li>
        </ul>
      </li>
      <li class="uk-nav-divider"></li>
      <li class="uk-parent">
        <a class="uk-active" href="{{ route('transfer.view.all') }}">{{ __('Transfers') }}</a>
        <ul class="uk-nav-sub">
          <li><a href="{{ route('transfer.view.all') }}">{{ __('View all') }}</a></li>
          <li><a href="{{ route('transfer.view.create') }}">{{ __('Create') }}</a></li>
        </ul>
      </li>
      <li class="uk-nav-divider"></li>
      <li class="uk-parent">
        <a class="uk-active" href="{{ route('wallet.view.all') }}">{{ __('Wallets') }}</a>
        <ul class="uk-nav-sub">
          <li><a href="{{ route('wallet.view.all') }}">{{ __('View all') }}</a></li>
          <li><a href="{{ route('wallet.view.create') }}">{{ __('Create') }}</a></li>
        </ul>
      </li>
    </ul>
  </div>
</div>
