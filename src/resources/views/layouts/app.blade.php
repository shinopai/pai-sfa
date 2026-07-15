<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>{{ config('app.name', 'PaiSFA') }}</title>

  @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>

<body>
  @include('layouts.header')

  <main class="l-main">
    <div class="u-wrap">
      @yield('content')
    </div>
  </main>
</body>

</html>
