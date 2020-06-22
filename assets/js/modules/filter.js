/**
 * Filter Controls for Results Sets
 */
import { enableSpinner, disableSpinner } from './spinner.js';
import { getXHRPromise } from './xhrPromise.js';

// Expect one content wrapper per page for result set
const filterResults = document.querySelector(`[data-filter="content"]`);
let filterPath;

/**
 * Remove Rows
 * Clears result set
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
 * Resets the current filter, but not others on page
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

    getXHRPromise(filterPath, selectedOptions)
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
 * Route to Request
 * @param {string} route
 */
const setFilterPath = function(route) {
    filterPath = route;
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

        getXHRPromise(filterPath, searchParams)
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
}

// Bind events
document.addEventListener("click", ApplyFilterControl, false);
document.addEventListener("click", clearFilterControl, false);
document.addEventListener("click", paginationControl, false);

export { setFilterPath, applyFilters };