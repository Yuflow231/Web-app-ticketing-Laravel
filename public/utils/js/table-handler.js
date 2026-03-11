// class that will handle all the table shenanigans
export class TableManager {
    /**
     * Constructor of the class handler
     * @param tableId reference to the table
     * @param rowsPerPage amount of element per page
     */
    constructor(tableId, rowsPerPage = 5) {
        this.tableBody = document.querySelector(`${tableId} tbody`);
        this.allRows = Array.from(this.tableBody.querySelectorAll('tr'));
        // create an independent copy of all the tables' rows
        this.filteredRows = [...this.allRows];

        this.rowsPerPage = rowsPerPage;
        this.currentPage = 1;

        // get filter elements
        this.searchInput = document.getElementById('search');
        this.filterSelects = document.querySelectorAll('select');

        // get UI elements
        this.paginationText = document.querySelectorAll('.page-header span, .page-footer span');
        this.prevBtns = document.querySelectorAll('.fa-angle-left');
        this.nextBtns = document.querySelectorAll('.fa-angle-right');

        this.init();
    }

    /*
     * Initiate all the event and execute the initial render of the table
     */
    init() {
        // ALWAYS sync filters with URL on page load (fixes browser cache issue)
        this.syncFiltersWithURL();

        // Also sync when page is loaded from back/forward cache
        window.addEventListener('pageshow', () => {
            this.syncFiltersWithURL();
        });

        // Search Event
        this.searchInput?.addEventListener('input', () => this.handleFilter());

        // Filter Dropdowns
        this.filterSelects.forEach(select => {
            select.addEventListener('change', (event) => this.addParam(event));
        });

        // Pagination Buttons
        this.prevBtns.forEach(btn => btn.parentElement.onclick = () => this.changePage(-1));
        this.nextBtns.forEach(btn => btn.parentElement.onclick = () => this.changePage(1));

        // Apply initial filters and render
        this.handleFilter();
    }

    /**
     * Sync form filter values with URL parameters
     * Used when navigating back/forward from browser cache
     */
    syncFiltersWithURL() {
        const url = new URL(location.href);

        // Sync search input
        if (this.searchInput) {
            this.searchInput.value = url.searchParams.get('search') || '';
        }

        // Sync select dropdowns
        this.filterSelects.forEach(select => {
            if (select.id) {
                const paramValue = url.searchParams.get(select.id);
                // If parameter exists in URL, use it; otherwise default to 'All'
                if (paramValue) {
                    select.value = paramValue;
                } else {
                    select.value = 'All';
                }
            }
        });
    }

    addParam(event){
        // Access the element's ID and current Value
        const elementId = event.target.id;
        const elementValue = event.target.value;

        // get the current url for later reload
        const url = new URL(location.href);

        url.searchParams.set(elementId, elementValue);

        // Only add hash for project-details page (check if we're on that page)
        if (location.pathname.includes('project-details') || document.getElementById('project-data-container')) {
            url.hash = 'table';
        }

        location.href = url.toString();
    }

    handleFilter() {
        // get the search field input
        const query = this.searchInput?.value.toLowerCase() || '';

        // Get current filter values from dropdowns
        const filters = {};
        this.filterSelects.forEach(select => {
            filters[select.id] = select.value;
        });

        // filter() returns the row solely when it gets true as the returned value (callback shenanigans)
        // when getting false, it skips the current element (in this case the row that doesn't match the filters)
        this.filteredRows = this.allRows.filter(row => {
            // check Search filter (checks all text in the row)
            const matchesSearch = row.innerText.toLowerCase().includes(query);

            // Check dropdown filters
            let matchesFilters = true;
            for (const [filterId, filterValue] of Object.entries(filters)) {
                // Skip if filter is set to "All"
                if (filterValue === 'All') continue;

                // Find the cell that corresponds to this filter
                const cell = row.querySelector(`[data-label="${filterId.charAt(0).toUpperCase() + filterId.slice(1)}"]`);
                if (cell) {
                    const cellText = cell.innerText.trim();
                    if (!cellText.includes(filterValue)) {
                        matchesFilters = false;
                        break;
                    }
                }
            }

            return matchesSearch && matchesFilters;
        });

        // Reset to page 1 on new search
        this.currentPage = 1;
        this.render();
    }

    /**
     * This function handle the page change of the table
     * @param direction value to change pages (-1) to go left, (1) to go right
     */
    changePage(direction) {
        // get the maximum of page available
        const maxPage = Math.ceil(this.filteredRows.length / this.rowsPerPage) || 1;
        const newPage = this.currentPage + direction;

        // condition to avoid going out of scope
        if (newPage >= 1 && newPage <= maxPage) {
            this.currentPage = newPage;
            this.render();
        }
    }

    /**
     * Update the visual of the table
     */
    render() {
        // get index of rows to render in the current page
        const start = (this.currentPage - 1) * this.rowsPerPage;
        const end = start + this.rowsPerPage;

        // create a reference to the rows that should be rendered on this page
        const visibleRows = this.filteredRows.slice(start, end);

        // toggle visibility
        this.allRows.forEach(row => {
            // check if the row is within visibility rows, if not -> hide it
            if (visibleRows.includes(row)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });

        // Update Pagination Text
        const maxPage = Math.ceil(this.filteredRows.length / this.rowsPerPage) || 1;
        this.paginationText.forEach(el => {
            el.innerText = `Page ${this.currentPage} of ${maxPage}`;
        });
    }
}
