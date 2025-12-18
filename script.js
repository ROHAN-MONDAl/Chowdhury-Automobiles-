// jQuery for Navbar (Keep this as is)
$(document).ready(function () {
    $(window).on('scroll', function () {
        if ($(this).scrollTop() > 50) {
            $('.navbar').addClass('shadow-sm');
        } else {
            $('.navbar').removeClass('shadow-sm');
        }
    });

    $(window).on('load', function () {
        $('#loading-screen').fadeOut(500);
    });
});

// Vanilla JS for Filter/Search with Error Handling
document.addEventListener('DOMContentLoaded', function () {
    try {
        console.log("Script Loaded: Filter Engine Started");

        // --- CONFIGURATION ---
        const itemsPerPage = 8;
        let currentPage = 1;
        let currentCategory = 'all';
        let searchQuery = '';

        // --- ELEMENTS & SAFETY CHECKS ---
        const allItems = document.querySelectorAll('.vehicle-item');
        const searchInput = document.querySelector('.hero-search-input');
        const mobileFilter = document.getElementById('mobileFilterSelect');
        const desktopChips = document.querySelectorAll('.filter-chip');
        const countLabel = document.querySelector('.vehicle-count');
        const paginationContainer = document.querySelector('.pagination');

        // Check if elements exist
        if (allItems.length === 0) {
            console.error("CRITICAL ERROR: No elements found with class 'vehicle-item'. Did you update the PHP file?");
            return; // Stop script to prevent errors
        }
        if (!searchInput) console.warn("Warning: Search input not found.");
        if (!countLabel) console.warn("Warning: Vehicle count label not found.");

        // --- INITIALIZATION ---
        filterAndPaginate();

        // --- EVENT LISTENERS ---

        // 1. Search Bar Listener
        if (searchInput) {
            searchInput.addEventListener('keyup', (e) => {
                searchQuery = e.target.value.toLowerCase().trim();
                currentPage = 1;
                filterAndPaginate();
            });
        }

        // 2. Mobile Filter Dropdown Listener
        if (mobileFilter) {
            mobileFilter.addEventListener('change', (e) => {
                currentCategory = e.target.value.toLowerCase();
                
                // Sync with desktop chips
                desktopChips.forEach(chip => {
                    chip.classList.remove('active');
                    // Safety check for chip text
                    const chipText = chip.innerText ? chip.innerText.toLowerCase() : '';
                    if (chipText.includes(currentCategory) || (currentCategory === 'all' && chipText.includes('all'))) {
                        chip.classList.add('active');
                    }
                });

                currentPage = 1;
                filterAndPaginate();
            });
        }

        // 3. Desktop Chips Listener
        desktopChips.forEach(chip => {
            chip.addEventListener('click', function () {
                desktopChips.forEach(c => c.classList.remove('active'));
                this.classList.add('active');

                const text = this.innerText.toLowerCase();
                
                // MAPPING LOGIC
                if (text.includes('all')) currentCategory = 'all';
                else if (text.includes('scooters')) currentCategory = 'scooters';
                else if (text.includes('mopeds')) currentCategory = 'mopeds';
                else if (text.includes('dirt')) currentCategory = 'dirt / off-road bikes';
                else if (text.includes('electric')) currentCategory = 'electric';
                else if (text.includes('cruiser')) currentCategory = 'cruiser';
                else if (text.includes('sport')) currentCategory = 'sport';
                else if (text.includes('touring')) currentCategory = 'touring';
                else if (text.includes('adventure')) currentCategory = 'adventure';
                else if (text.includes('naked')) currentCategory = 'naked';
                else if (text.includes('cafe')) currentCategory = 'cafe';
                else if (text.includes('bobbers')) currentCategory = 'bobbers';
                else if (text.includes('choppers')) currentCategory = 'choppers';
                else if (text.includes('pocket')) currentCategory = 'pocket';
                else currentCategory = text.trim();

                if (mobileFilter) mobileFilter.value = currentCategory;
                currentPage = 1;
                filterAndPaginate();
            });
        });

        // --- MAIN LOGIC ---
        function filterAndPaginate() {
            let visibleItems = [];

            allItems.forEach((item, index) => {
                // SAFETY: Fallback to empty string if attribute is null
                const itemCategory = (item.getAttribute('data-category') || '').toLowerCase();
                const itemName = (item.getAttribute('data-name') || '').toLowerCase();
                const itemText = item.innerText.toLowerCase();

                // Debug first item to ensure data is reading
                if (index === 0) {
                   // console.log("Debug Item 1:", { cat: itemCategory, name: itemName });
                }

                // CHECK: Category
                const categoryMatch = (currentCategory === 'all') || (itemCategory.includes(currentCategory));

                // CHECK: Search
                const searchMatch = (itemName.includes(searchQuery)) || (itemText.includes(searchQuery));

                if (categoryMatch && searchMatch) {
                    visibleItems.push(item);
                } else {
                    item.style.display = 'none';
                }
            });

            // Update Count
            if (countLabel) countLabel.innerText = `${visibleItems.length} Vehicles`;

            // Pagination Logic
            const totalPages = Math.ceil(visibleItems.length / itemsPerPage);
            if (currentPage > totalPages) currentPage = 1;
            if (currentPage < 1 && totalPages > 0) currentPage = 1;

            const startIndex = (currentPage - 1) * itemsPerPage;
            const endIndex = startIndex + itemsPerPage;

            visibleItems.forEach((item, index) => {
                if (index >= startIndex && index < endIndex) {
                    item.style.display = 'block';
                    item.style.opacity = '1'; // Removed timeout for stability
                } else {
                    item.style.display = 'none';
                }
            });

            if (paginationContainer) renderPaginationControls(totalPages);
        }

        function renderPaginationControls(totalPages) {
            paginationContainer.innerHTML = '';
            if (totalPages <= 1) return;

            // Prev
            const prevLi = document.createElement('li');
            prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
            prevLi.innerHTML = `<a class="page-link" href="#">&laquo;</a>`;
            prevLi.onclick = (e) => { e.preventDefault(); if (currentPage > 1) { currentPage--; filterAndPaginate(); scrollToTop(); }};
            paginationContainer.appendChild(prevLi);

            // Numbers
            for (let i = 1; i <= totalPages; i++) {
                const li = document.createElement('li');
                li.className = `page-item ${i === currentPage ? 'active' : ''}`;
                li.innerHTML = `<a class="page-link" href="#">${i}</a>`;
                li.onclick = (e) => { e.preventDefault(); currentPage = i; filterAndPaginate(); scrollToTop(); };
                paginationContainer.appendChild(li);
            }

            // Next
            const nextLi = document.createElement('li');
            nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
            nextLi.innerHTML = `<a class="page-link" href="#">&raquo;</a>`;
            nextLi.onclick = (e) => { e.preventDefault(); if (currentPage < totalPages) { currentPage++; filterAndPaginate(); scrollToTop(); }};
            paginationContainer.appendChild(nextLi);
        }

        function scrollToTop() {
            const section = document.getElementById('inventory');
            if (section) section.scrollIntoView({ behavior: 'smooth' });
        }

    } catch (error) {
        console.error("JAVASCRIPT ERROR:", error);
    }
});