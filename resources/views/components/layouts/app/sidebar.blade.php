<!doctype html>
<html lang="ar" dir="rtl">
  <head>
    @include('partials.head-admin')
  </head>

  <body class="bg-gray-100 dark:bg-gray-900 font-sans">
    <div class="relative flex h-screen overflow-hidden">
      <div id="sidebar-overlay" class="hidden fixed inset-0 z-40 bg-black/40 md:hidden"></div>

      <div id="app-sidebar" class="fixed inset-y-0 right-0 z-50 w-64 overflow-y-auto transform translate-x-full transition-transform duration-200 md:translate-x-0 md:static md:inset-auto md:transform-none">
        @include('partials.sidebar')
      </div>

      <main class="flex-1 w-full p-4 sm:p-6 md:p-8 overflow-y-auto">
        <div class="md:hidden flex items-center justify-between mb-4">
          <button id="sidebar-open-btn" type="button" class="inline-flex items-center justify-center w-11 h-11 rounded-xl border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 dark:hover:bg-gray-700" aria-label="menu">
            <i data-lucide="menu" class="w-5 h-5"></i>
          </button>
          <div class="text-sm font-semibold text-gray-700 dark:text-gray-200 truncate">{{ $title ?? '' }}</div>
        </div>

        {{ $slot }}
      </main>
    </div>

    <script src="{{ asset('tradyshop/js/shared.js') }}" defer></script>
    <script src="{{ asset('tradyshop/js/admin.js') }}" defer></script>
    <script>
      (function () {
        var sidebar = document.getElementById('app-sidebar');
        var overlay = document.getElementById('sidebar-overlay');
        var openBtn = document.getElementById('sidebar-open-btn');
        if (!sidebar || !overlay || !openBtn) return;

        var isOpen = function () {
          return !sidebar.classList.contains('translate-x-full');
        };

        var openSidebar = function () {
          sidebar.classList.remove('translate-x-full');
          overlay.classList.remove('hidden');
          document.body.classList.add('overflow-hidden');
        };

        var closeSidebar = function () {
          sidebar.classList.add('translate-x-full');
          overlay.classList.add('hidden');
          document.body.classList.remove('overflow-hidden');
        };

        openBtn.addEventListener('click', function () {
          if (isOpen()) {
            closeSidebar();
            return;
          }
          openSidebar();
        });
        overlay.addEventListener('click', closeSidebar);

        window.addEventListener('resize', function () {
          if (window.innerWidth >= 768) {
            overlay.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            sidebar.classList.remove('translate-x-full');
            return;
          }
          closeSidebar();
        });

        closeSidebar();
      })();
    </script>
  </body>
</html>
