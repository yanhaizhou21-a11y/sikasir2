// cashier-main.js - Main Cashier System

// Global variables
let currentViewMode = "detailed";
let currentCategory = "all";

// Initialize order items if not already done
if (typeof window.orderItems === 'undefined') {
    window.orderItems = [];
}
if (typeof window.orderCounter === 'undefined') {
    window.orderCounter = 1;
}

let orderItems = window.orderItems;
let orderCounter = window.orderCounter;

// Initialize on page load
document.addEventListener("DOMContentLoaded", () => {
    initializeCashierSystem();
});

function initializeCashierSystem() {
    const toggle = document.getElementById("view-mode-toggle");
    const filterButtons = document.querySelectorAll(".filter-btn");
    const searchInput = document.getElementById("searchInput");
    const clearButton = document.getElementById("clearSearch");

    // Update current date and time
    updateDateTime();
    setInterval(updateDateTime, 1000);

    // Initialize view
    updateView();

    // View mode toggle event listener
    if (toggle) {
        toggle.addEventListener("change", () => {
            currentViewMode = toggle.checked ? "card" : "detailed";
            console.log("View mode changed to:", currentViewMode);
            updateView();
        });
    }

    // Filter buttons event listeners
    filterButtons.forEach((button) => {
        button.addEventListener("click", (e) => {
            e.preventDefault();

            // Remove active class from all buttons
            filterButtons.forEach((btn) => btn.classList.remove("active"));

            // Add active class to clicked button
            button.classList.add("active");

            // Update active button style
            filterButtons.forEach((btn) => {
                btn.style.color = "#6b7280"; // gray-500
                btn.style.borderBottomColor = "transparent";
            });

            button.style.color = "#ea580c"; // orange-600
            button.style.borderBottomColor = "#ea580c";

            currentCategory = button.getAttribute("data-category");
            console.log("Category changed to:", currentCategory);
            updateView();
        });
    });

    // Search functionality with real-time search
    if (searchInput) {
        searchInput.addEventListener("input", function () {
            const searchTerm = this.value.toLowerCase().trim();

            // Show/hide clear button
            if (searchTerm.length > 0) {
                clearButton.classList.remove("hidden");
            } else {
                clearButton.classList.add("hidden");
            }

            // Perform real-time search
            performSearch(searchTerm);
        });

        // Clear search functionality
        clearButton?.addEventListener("click", clearSearchInput);
    }

    // Initialize order display
    updateOrderDisplay();
    updateTotals();
}

// Real-time search function
function performSearch(searchTerm) {
    const detailedItems = document.querySelectorAll(
        ".detailed-view.product-item"
    );
    const cardItems = document.querySelectorAll(".card-view.product-item");

    // Search in detailed view
    detailedItems.forEach((item) => {
        const productName =
            item.getAttribute("data-product-name")?.toLowerCase() || "";
        const category =
            item.getAttribute("data-category")?.toLowerCase() || "";

        const matchesSearch =
            searchTerm === "" ||
            productName.includes(searchTerm) ||
            category.includes(searchTerm);
        const matchesCategory =
            currentCategory === "all" ||
            item.getAttribute("data-category") === currentCategory;

        if (matchesSearch && matchesCategory) {
            item.style.display = "block";
        } else {
            item.style.display = "none";
        }
    });

    // Search in card view
    cardItems.forEach((item) => {
        const productName =
            item.getAttribute("data-product-name")?.toLowerCase() || "";
        const category =
            item.getAttribute("data-category")?.toLowerCase() || "";

        const matchesSearch =
            searchTerm === "" ||
            productName.includes(searchTerm) ||
            category.includes(searchTerm);
        const matchesCategory =
            currentCategory === "all" ||
            item.getAttribute("data-category") === currentCategory;

        if (matchesSearch && matchesCategory) {
            item.style.display = "block";
        } else {
            item.style.display = "none";
        }
    });
}

function clearSearchInput() {
    const searchInput = document.getElementById("searchInput");
    const clearButton = document.getElementById("clearSearch");

    if (searchInput) {
        searchInput.value = "";
        clearButton?.classList.add("hidden");

        // Reset to normal filter view
        updateView();
    }
}

// View and filter functions
function updateView() {
    const detailedContainer = document.getElementById("detailed-view");
    const cardContainer = document.getElementById("card-view");

    if (!detailedContainer || !cardContainer) {
        console.error("View containers not found");
        return;
    }

    // Toggle view containers
    if (currentViewMode === "card") {
        detailedContainer.classList.add("hidden");
        cardContainer.classList.remove("hidden");
    } else {
        detailedContainer.classList.remove("hidden");
        cardContainer.classList.add("hidden");
    }

    // Apply filters
    const detailedItems = document.querySelectorAll(
        ".detailed-view.product-item"
    );
    const cardItems = document.querySelectorAll(".card-view.product-item");

    filterItems(detailedItems);
    filterItems(cardItems);

    console.log(
        `View updated: ${currentViewMode}, Category: ${currentCategory}`
    );
}

function filterItems(items) {
    items.forEach((item) => {
        const itemCategory = item.getAttribute("data-category");

        if (currentCategory === "all" || itemCategory === currentCategory) {
            item.style.display = "block";
        } else {
            item.style.display = "none";
        }
    });
}

// Filter scroll functionality
function scrollFilter(direction) {
    const filterMenu = document.getElementById("filterMenu");
    if (!filterMenu) return;

    const scrollAmount = 200;

    if (direction === "left") {
        filterMenu.scrollLeft -= scrollAmount;
    } else {
        filterMenu.scrollLeft += scrollAmount;
    }
}

// Export functions for global access
window.updateView = updateView;
window.scrollFilter = scrollFilter;
window.clearSearchInput = clearSearchInput;
window.currentViewMode = currentViewMode;
window.orderItems = window.orderItems || orderItems;
window.orderCounter = window.orderCounter || orderCounter;
// Function to update date and time display     