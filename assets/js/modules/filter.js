/**
 * Filter and Search Controls for Results Sets
 *
 * HTML
 * Import /includes/_pitonMacros.html
 * Echo filterSearch() and filterOptions()
 *
 * Add data-filter="content" on the parent elment containing the result set.
 * When the query is executed the direct children of data-filter="content" are removed and replaced
 *
 * JS
 * Import
 *   import { setFilterPath } from "./modules/filter.js";
 *
 * Define query endpoint in main script
 *   setFilterPath("path/to/query/endpoint")
 *
 */
import { enableSpinner, disableSpinner } from './spinner.js';
import { getXHRPromise } from './xhrPromise.js';

// Expect one content wrapper (data-filter="content") per page for result set
const filterResults = document.querySelector(`[data-filter="content"]`);

// Hoist request route for filter search
let filterPath;

/**
 * Set Route to Request Endpoint
 * Exported to calling file to set route
 * @param {string} route
 */
const setFilterPath = function(route) {
    filterPath = route;
}

/**
 * Remove Rows
 * Clears result set from filterResults constant
 */
const removeRows = function() {
    if (filterResults) {
        while (filterResults.firstChild) {
            filterResults.removeChild(filterResults.lastChild);
        }
    }
}

/**
 * Clear This Filter Control
 * Resets the current filter, but not other filters
 * @param {Event} event
 */
const clearFilterControl = function(event) {
    if (event.target.dataset.filterControl === "clear") {
        let filter = event.target.closest(`[data-filter="options"]`);
        filter.querySelectorAll("input").forEach(input => {
            input.checked = false;
        });
    }
}

/**
 * Apply Filter Control
 * @param {Event} event
 */
const ApplyFilterControl = function(event) {
    if (filterPath && filterResults && event.target.dataset.filterControl === "apply") {
        applyFilters();
    }
}

/**
 * Private: Get Filter XHR Promise
 * @param {object} options
 */
const getFilterXHRPromise = function(options) {
    return getXHRPromise(filterPath, options)
        .then((data) => {
            removeRows();
            return data;
        })
        .then(data => {
            filterResults.insertAdjacentHTML('afterbegin', data);
        })
        .then(() => {
            disableSpinner();
        });
}

/**
 * Apply Filters
 * Applies all filters on page as single XHR request
 */
const applyFilters = function() {
    let filters = document.querySelectorAll(`[data-filter="options"] input`);
    let selectedOptions = {};
    enableSpinner();

    // Get filter options
    filters.forEach((input) => {
        if (input.checked) {
            // Check if this property has already been set, in which case concatenate value
            if (selectedOptions.hasOwnProperty(input.name)) {
                selectedOptions[input.name] += "," + input.value;
            } else {
                selectedOptions[input.name] = input.value;
            }
        }
    });

    return getFilterXHRPromise(selectedOptions);
}

/**
 * Text Search
 */
const search = function() {
    let terms = document.querySelector(`[data-filter="search"] input`);
    let query = {"terms": terms.value};
    enableSpinner();

    return getFilterXHRPromise(query);
}

/**
 * Pagination Controls
 * Interrupts page link request to submit as XHR to keep control filter state
 * @param {Event} event
 */
const paginationControl = function(event) {
    if (event.target.closest(".pagination > div")) {
        event.preventDefault();
        enableSpinner();

        // Get query string parameters from pagination link and submit to XHRPromise as a URLSearchParams object
        let link = event.target.closest(".pagination > div").querySelector("a").href;
        let url = new URL(link);
        let searchParams = new URLSearchParams(url.search);

        return getFilterXHRPromise(searchParams);
    }
}

// Bind events
// There may be more than one filter control on the page
document.addEventListener("click", ApplyFilterControl, false);
document.addEventListener("click", clearFilterControl, false);
document.addEventListener("click", paginationControl, false);

// There should be only one search control per page, so binding directly to element
document.querySelector(`[data-filter-control="search"]`)?.addEventListener("click", search, false);
document.querySelector(`[data-filter="search"] input`)?.addEventListener("keypress", (event) => {
    if (event.key === 'Enter') search();
}, false);

export { setFilterPath, applyFilters };