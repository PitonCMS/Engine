// --------------------------------------------------------
// Media management
// --------------------------------------------------------

import './modules/main.js';
import { enableSpinner, disableSpinner } from './modules/spinner.js';
import { postXHRPromise } from './modules/xhrPromise.js';
import { alertInlineMessage } from './modules/alert.js';

const categoryElement = document.querySelector(`[data-media-category="spare"] > div`);
let elementKey = 0;

/**
 * Add Category Input
 */
const addCategory = function() {
    // Clone spare category element and set unique name array key
    let newCategory = categoryElement.cloneNode(true);
    let arrayKey = (elementKey++) + "n";
    newCategory.querySelectorAll(`input[name^=category]`).forEach(input => {
        input.name = input.name.replace(/(.+?\[)(\].+)/, "$1" + arrayKey + "$2");
    });

    document.querySelector(`[data-category="wrapper"]`).appendChild(newCategory);
}

/**
 * Delete Category
 * @param {Event} event
 */
const deleteCategory = function(event) {
    if (!event.target.dataset.deleteCategoryPrompt) return;
    if (!confirm(event.target.dataset.deleteCategoryPrompt)) return;

    let categoryId = parseInt(event.target.dataset.categoryId);
    let catElement = event.target.closest(`[data-category="parent"]`);

    if (isNaN(categoryId)) {
        // Not saved yet, just remove
        catElement.remove();
    } else {
        // Delete from DB
        enableSpinner();
        let data = {"categoryId": categoryId};
        postXHRPromise(pitonConfig.routes.adminMediaCategoryDelete, data)
            .then(() => {
                catElement.remove();
            })
            .catch((text) => {
                console.log("Failed to delete category: ", text);
                alertInlineMessage('danger', 'Failed to Delete Category', text);
                disableSpinner();
        });
    }
}

// Bind events
document.querySelector(`[data-category="add"]`).addEventListener("click", addCategory, false);
document.addEventListener("click", deleteCategory, false);
