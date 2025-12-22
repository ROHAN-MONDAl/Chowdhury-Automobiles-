 // Simple JS helper to open modals
        function openModal(modalId) {
            var myModal = new bootstrap.Modal(document.getElementById(modalId));
            myModal.show();
        }

        // Hide loader when page loads
        window.addEventListener('load', function() {
            document.getElementById('loader').style.display = 'none';
        });

        $(document).ready(function() {
            // Listen for typing in the search box
            $("#leadSearchInput").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                var hasVisibleItems = false;

                // Loop through all items with class 'lead-item'
                $("#leadsContainer .lead-item").filter(function() {
                    // Check if the text inside the card matches the search value
                    var isMatch = $(this).text().toLowerCase().indexOf(value) > -1;

                    // Toggle visibility
                    $(this).toggle(isMatch);

                    // If we found a match, mark flag as true
                    if (isMatch) hasVisibleItems = true;
                });

                // Show "No Results" message if everything is hidden
                if (!hasVisibleItems && value.length > 0) {
                    $("#noResultsMsg").show();
                } else {
                    $("#noResultsMsg").hide();
                }
            });
        });