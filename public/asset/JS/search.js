// Function to handle search
function handleSearch() {
    // Get the value entered in the search input
    var searchTerm = document.getElementsByName('table_search')[0].value.trim().toLowerCase();

    // Get all the rows in the table body
    var rows = document.querySelectorAll('.table tbody tr');

    // Variable to track if any rows are visible after filtering
    var visibleRows = 0;

    // Loop through all rows
    rows.forEach(function (row) {
        // Skip the "Result Not Found" row during search
        if (row.id === 'no-results-row') return;

        // Get all cells in the row
        var cells = row.querySelectorAll('td');

        // Variable to check if search term is found in any cell of the row
        var found = false;

        // Loop through all cells
        cells.forEach(function (cell) {
            // Check if the cell contains the search term
            if (cell.textContent.trim().toLowerCase().includes(searchTerm)) {
                // If yes, set found to true
                found = true;
            }
        });

        // If the search term is found, display the row, otherwise hide it
        row.style.display = found ? '' : 'none';

        // Count the number of visible rows
        if (found) visibleRows++;
    });

    // Show or hide the "Result Not Found" row based on visibleRows count
    var noResultsRow = document.getElementById('no-results-row');
    noResultsRow.style.display = visibleRows > 0 ? 'none' : '';
}

// Add event listener to the search input for keyup event
document.getElementsByName('table_search')[0].addEventListener('keyup', handleSearch);
