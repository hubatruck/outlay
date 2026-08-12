<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'Laravel') }}</title>

  <!-- Scripts -->
  @vite('resources/js/bootstrap.js')
  <script>
    window.runWithJQuery = function (fn) {
      if (window.jQuery) return fn();
      const id = setInterval(() => {
        if (window.jQuery) {
          clearInterval(id);
          fn();
        }
      }, 10);
    };
  </script>

  <!-- Styles -->
  @vite('resources/less/app.less')
</head>

<body>
  <div id="app">
    <x-navbar />

    <x-status-alert />

    <main>
      <section class="uk-section uk-padding-small">
        <div class="uk-container">
          <div class="uk-flex-center uk-child-width-expand@l">
            <div class="uk-card uk-card-default">
              @yield('content')
            </div>
          </div>
        </div>
      </section>
    </main>

    @auth
      <x-sidenav />
    @endauth
  </div>

  <!-- App -->
  @vite('resources/js/app.js')
  @stack('scripts')

</body>

</html>