<!DOCTYPE html>
<html lang="ar" dir="rtl">
  <head>
    @include('partials.head')
  </head>
  <body class="min-h-screen bg-gradient-to-br from-rose-50 via-orange-50 to-white dark:from-gray-900 dark:via-gray-900 dark:to-gray-800 flex items-center justify-center p-4 font-sans">
    {{ $slot }}
    <script src="{{ asset('tradyshop/js/shared.js') }}" defer></script>
  </body>
</html>
