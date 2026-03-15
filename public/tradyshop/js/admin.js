(function () {
  function initIcons() {
    if (window.lucide && typeof window.lucide.createIcons === "function") {
      window.lucide.createIcons();
    }
  }

  function setupTableSearch(inputId, tableBodyId) {
    const searchInput = document.getElementById(inputId);
    const tableBody = document.getElementById(tableBodyId);
    if (!searchInput || !tableBody) return;

    const getRows = () => Array.from(tableBody.getElementsByTagName("tr"));

    searchInput.addEventListener("input", () => {
      const searchTerm = (searchInput.value || "").toLowerCase().trim();

      getRows().forEach((row) => {
        const rowText = (row.textContent || "").toLowerCase();
        row.style.display = rowText.includes(searchTerm) ? "" : "none";
      });
    });
  }

  function initAdmin() {
    initIcons();

    setupTableSearch("customer-search", "customer-table-body");
    setupTableSearch("supplier-search", "supplier-table-body");
    setupTableSearch("product-search", "product-table-body");
    setupTableSearch("sales-search", "sales-table-body");
    setupTableSearch("commission-search", "commission-table-body");
    setupTableSearch("order-search", "order-table-body");
    setupTableSearch("card-search", "card-table-body");
    setupTableSearch("withdraw-search", "withdraw-table-body");
    setupTableSearch("expense-search", "expense-table-body");
    setupTableSearch("ads-search", "ads-table-body");
    setupTableSearch("shipping-search", "shipping-table-body");
    setupTableSearch("debt-search", "debt-table-body");
    setupTableSearch("user-search", "user-table-body");
  }

  document.addEventListener("DOMContentLoaded", initAdmin);
})();
