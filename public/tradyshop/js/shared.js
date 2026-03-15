(function () {
  function initDarkMode() {
    const html = document.documentElement;
    const toggle = document.getElementById("dark-mode-toggle");

    if (localStorage.getItem("darkMode") === "enabled") {
      html.classList.add("dark");
      if (toggle) toggle.checked = true;
    }

    if (toggle) {
      toggle.addEventListener("change", () => {
        if (toggle.checked) {
          html.classList.add("dark");
          localStorage.setItem("darkMode", "enabled");
        } else {
          html.classList.remove("dark");
          localStorage.setItem("darkMode", "disabled");
        }
      });
    }
  }

  function setActiveNavLink() {
    const navLinks = document.querySelectorAll(".nav-link");
    if (!navLinks.length) return;

    const currentPath = window.location.pathname;

    navLinks.forEach((link) => {
      const href = link.getAttribute("href") || "";
      let targetPath = "";

      try {
        targetPath = new URL(href, window.location.origin).pathname;
      } catch {
        targetPath = href;
      }

      const isActive = targetPath === currentPath;

      if (isActive) {
        link.classList.add(
          "active",
          "bg-gradient-to-r",
          "from-rose-400",
          "to-orange-300",
          "text-white",
        );
        link.classList.remove(
          "text-gray-700",
          "hover:bg-gray-200",
          "dark:text-gray-200",
          "dark:hover:bg-gray-700",
        );
      } else {
        link.classList.remove(
          "active",
          "bg-gradient-to-r",
          "from-rose-400",
          "to-orange-300",
          "text-white",
        );
        link.classList.add(
          "text-gray-700",
          "hover:bg-gray-200",
          "dark:text-gray-200",
          "dark:hover:bg-gray-700",
        );
      }
    });
  }

  function initIcons() {
    if (window.lucide && typeof window.lucide.createIcons === "function") {
      window.lucide.createIcons();
    }
  }

  function initLayout() {
    initDarkMode();
    setActiveNavLink();
    initIcons();
  }

  document.addEventListener("DOMContentLoaded", initLayout);
})();
