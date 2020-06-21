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
 * Clear This Filter
 * Resets the current filter, but not others on page
 * @param {Event} event
 */
const clearFilter = function(event) {
    if (event.target.dataset.filterControl === "clear") {
        let filter = event.target.closest(`[data-filter="options"]`);
        filter.querySelectorAll("input").forEach(input => {
            input.checked = false;
        });
    }
}

/**
 * Apply Filters
 * Applies all filters on page as single XHR request
 * @param {Event} event
 */
const applyFilters = function(event) {
    if (filterPath && filterResults && event.target.dataset.filterControl === "apply") {
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
}

/**
 * Route to Request
 * @param {string} route
 */
const setFilterPath = function(route) {
    filterPath = route;
}

// Bind events
document.addEventListener("click", applyFilters, false);
document.addEventListener("click", clearFilter, false);

export { setFilterPath };