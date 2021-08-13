<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'Laravel') }}</title>

  <!-- Scripts -->
  <script src="{{ mix('js/manifest.js') }}"></script>
  <script src="{{ mix('js/vendor.js') }}"></script>

  <!-- Styles -->
  <link rel="stylesheet" href="{{ mix('css/app.css') }}"/>
</head>

<body>
<div id="app">
  <x-navbar/>

  <x-status-alert/>

  <main>
    <section class="uk-section">
      <div class="uk-container">
        <div class="uk-flex-center uk-child-width-expand@l">
          <div class="uk-card uk-card-default">
            @yield('content')
          </div>
        </div>
      </div>
    </section>
  </main>
</div>

<!-- App -->
<script src="{{ mix('js/app.js') }}"></script>
<script src="{{ asset('vendor/datatables/datatables.bootstrap5.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/buttons.server-side.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/extensions.min.js') }}"></script>
@stack('scripts')

</body>
</html>
