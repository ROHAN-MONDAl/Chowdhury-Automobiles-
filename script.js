$(document).ready(function () {
    // Navbar blur on scroll
    $(window).on('scroll', function () {
        if ($(this).scrollTop() > 50) {
            $('.navbar').addClass('shadow-sm');
        } else {
            $('.navbar').removeClass('shadow-sm');
        }
    });

    // Fade out loader once page is loaded
    $(window).on('load', function () {
        $('#loading-screen').fadeOut(500);
    });

    // --------------------------------------------
    // CONFIG
    // --------------------------------------------
    const itemsPerPage = 6; // Set to 6 or 9 for better grid view
    // Important: Select the column (.col), not the card, so the grid doesn't break
    const allItems = $(".row .col");
    let matchedItems = allItems; // This stores the filtered list (even if hidden)

    // --------------------------------------------
    // 1. UPDATE COUNT
    // --------------------------------------------
    function updateCount() {
        $(".vehicle-count").text(matchedItems.length + " Vehicles");
    }

    // --------------------------------------------
    // 2. BUILD PAGINATION UI
    // --------------------------------------------
    function buildPaginationUI() {
        let totalPages = Math.ceil(matchedItems.length / itemsPerPage);
        let paginationHTML = `
        <li class="page-item prev disabled"><a class="page-link" href="#">Prev</a></li>
    `;

        for (let i = 1; i <= totalPages; i++) {
            paginationHTML += `
            <li class="page-item page-num" data-page="${i}">
                <a class="page-link" href="#">${i}</a>
            </li>
        `;
        }

        paginationHTML += `
        <li class="page-item next"><a class="page-link" href="#">Next</a></li>
    `;

        $(".pagination").html(paginationHTML);
    }

    // --------------------------------------------
    // 3. SHOW SPECIFIC PAGE
    // --------------------------------------------
    function showPage(page) {
        let totalPages = Math.ceil(matchedItems.length / itemsPerPage);

        // Calculate start/end based on the MASTER list (matchedItems), not the visible DOM
        let start = (page - 1) * itemsPerPage;
        let end = start + itemsPerPage;

        // Hide EVERYTHING first
        allItems.hide();

        // Show only the specific slice of the master list
        matchedItems.slice(start, end).fadeIn(200);

        // Update Buttons
        $(".pagination .page-item").removeClass("active");
        $(`.pagination .page-item[data-page="${page}"]`).addClass("active");

        $(".prev").toggleClass("disabled", page === 1);
        $(".next").toggleClass("disabled", page === totalPages || totalPages === 0);
    }

    // --------------------------------------------
    // 4. MASTER FILTER (Search + Category)
    // --------------------------------------------
    function applyFilters() {
        let searchValue = $(".hero-search-input").val().toLowerCase();
        let categoryFilter = "";

        // --- MODIFIED SECTION START ---
        // Check if the Mobile Dropdown is visible
        if ($('#mobileFilterSelect').is(':visible')) {
            // Read text from the selected dropdown option
            categoryFilter = $('#mobileFilterSelect option:selected').text().trim().toLowerCase();
        } else {
            // Read text from the active desktop chip
            categoryFilter = $(".filter-chip.active").text().trim().toLowerCase();
        }
        // --- MODIFIED SECTION END ---

        // Filter the original list of all items
        matchedItems = allItems.filter(function () {
            let cardText = $(this).text().toLowerCase();

            // 1. Check Search
            let matchesSearch = cardText.includes(searchValue);

            // 2. Check Category
            let matchesCategory = false;

            if (categoryFilter === "all vehicles") {
                matchesCategory = true;
            } else if (categoryFilter.includes("scooter")) {
                matchesCategory = cardText.includes("scooter") || cardText.includes("activa");
            } else if (categoryFilter.includes("mopeds")) {
                matchesCategory = cardText.includes("mopeds");
            } else if (categoryFilter.includes("dirt / off-road bikes")) {
                matchesCategory = cardText.includes("dirt / off-road bikes");
            } else if (categoryFilter.includes("electric")) {
                matchesCategory = cardText.includes("electric");
            } else if (categoryFilter.includes("cruiser")) {
                matchesCategory = cardText.includes("cruiser");
            } else if (categoryFilter.includes("sport")) {
                matchesCategory = cardText.includes("sport");
            } else if (categoryFilter.includes("touring")) {
                matchesCategory = cardText.includes("touring");
            } else if (categoryFilter.includes("adventure")) {
                matchesCategory = cardText.includes("adventure") || cardText.includes("dual-sport");
            } else if (categoryFilter.includes("naked")) {
                matchesCategory = cardText.includes("naked") || cardText.includes("standard");
            } else if (categoryFilter.includes("cafe")) {
                matchesCategory = cardText.includes("cafe");
            } else if (categoryFilter.includes("bobber")) {
                matchesCategory = cardText.includes("bobber");
            } else if (categoryFilter.includes("chopper")) {
                matchesCategory = cardText.includes("chopper");
            } else if (categoryFilter.includes("pocket") || categoryFilter.includes("mini")) {
                matchesCategory = cardText.includes("pocket") || cardText.includes("mini");
            }

            return matchesSearch && matchesCategory;
        });

        updateCount();
        buildPaginationUI();

        if (matchedItems.length > 0) {
            showPage(1);
        } else {
            allItems.hide();
            $(".pagination").empty();
        }
    }

    // ==================================================
    // 3. ADD THESE EVENT LISTENERS TO MAKE IT WORK
    // ==================================================
    $(document).ready(function () {

        // Trigger filter when Mobile Dropdown changes
        $('#mobileFilterSelect').on('change', function () {
            applyFilters();
        });

        // Trigger filter when Desktop Chip is clicked (Existing logic)
        $('.filter-chip').on('click', function () {
            $('.filter-chip').removeClass('active');
            $(this).addClass('active');
            applyFilters();
        });

        // Trigger filter when typing in search
        $(".hero-search-input").on("keyup", function () {
            applyFilters();
        });
    });


    // --------------------------------------------
    // EVENTS
    // --------------------------------------------

    // Search Input
    $(".hero-search-input").on("keyup", function () {
        applyFilters();
    });

    // Search Button
    $(".hero-search-container button").on("click", function () {
        applyFilters();
    });

    // Filter Chips
    $(".filter-chip").on("click", function () {
        $(".filter-chip").removeClass("active");
        $(this).addClass("active");
        applyFilters();
    });

    // Pagination Number Click
    $(document).on("click", ".page-num", function (e) {
        e.preventDefault();
        let page = parseInt($(this).attr("data-page"));
        showPage(page);
    });

    // Prev Button
    $(document).on("click", ".prev", function (e) {
        e.preventDefault();
        if ($(this).hasClass("disabled")) return;
        let active = parseInt($(".pagination .active").attr("data-page"));
        showPage(active - 1);
    });

    // Next Button
    $(document).on("click", ".next", function (e) {
        e.preventDefault();
        if ($(this).hasClass("disabled")) return;
        let active = parseInt($(".pagination .active").attr("data-page"));
        let max = Math.ceil(matchedItems.length / itemsPerPage);
        if (active < max) showPage(active + 1);
    });

    // --------------------------------------------
    // INIT
    // --------------------------------------------
    $(document).ready(function () {
        applyFilters();
    });

});

