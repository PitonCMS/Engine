/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright 2018 Wolfgang Moritz
 * @license   AGPL-3.0-or-later with Theme Exception. See LICENSE file for details.. See LICENSE file for details.
 */

/**
 * Toggle Collapse of Elements Module
 *
 * When the element with data-collapse-toggle="<key>" is clicked the element with data-collapse-target="<key>"
 * has the "collapsed" class toggled to animate a slide up or down.
 *
 * Add data-collapse-auto="<key>" to any target element to listen for a click event and apply the collapsed class
 *
 * HTML
 * Add data-collapse-toggle="<key>" (with a unique key value) on the element to click and trigger a toggle collapse.
 * Add data-collapse-target="<key>" (with the same key value) on the element to be collapsed.
 * Optionally add data-collapse-auto="<key>" (with the same key value) to any other element that can click collapse the target.
 * To load the page to a collapsed state, add the class "collapsed" on the target.
 *
 * JS
 * Import this file.
 */

// Class names
const collapseClass = "collapsed";
const hideClass = "d-none";

// Find all page edit block data-collapse-toggle=newElementButton* elements, and convert to an array
const blockNewElementButtons = Array.from(document.querySelectorAll(`[data-collapse-toggle^="newElementButton"]`));

/**
 * Collapse Toggle
 * @param {Event} event
 */
const collapseToggle = function (event) {
    if (!event.target.closest(`[data-collapse-toggle]`)) return;

    // Find the matching collapse target by key
    let toggleKey = event.target.closest(`[data-collapse-toggle]`).dataset.collapseToggle;

    // If page edit add new element toggle, then hide lower toggle lists
    if (toggleKey.match(/^newElementButton/)) {
        toggleLowerNewElementButtons(toggleKey);
    }

    // Apply toggle class to target
    let collapseTarget = document.querySelector(`[data-collapse-target="${toggleKey}"]`);
    collapseTarget.classList.toggle(collapseClass);
}

/**
 * Auto Collapse
 * @param {Event} event
 */
const autoCollapse = function (event) {
    if (!event.target.closest(`[data-collapse-auto]`)) return;

    // Find the matching collapse target by key and apply toggle class (not toggle)
    let toggleKey = event.target.closest(`[data-collapse-auto]`).dataset.collapseAuto;
    let collapseTarget = document.querySelector(`[data-collapse-target="${toggleKey}"]`);
    collapseTarget.classList.add(collapseClass);

    // If page edit add new element toggle, then also hide lower toggle lists
    if (toggleKey.match(/^newElementButton/)) {
        toggleLowerNewElementButtons(toggleKey);
    }
}

/**
 * Toggle Lower New Element Button Collapse
 *
 * Because "Add Element" has a unique condition where the toggled target is behind other data-collapse-toggle New Elements
 * lower in the page, we need to add and remove the class d-none to those new elements *after* this one
 * @param {string} key
 */
const toggleLowerNewElementButtons = function(key) {
    // Get index of the current toggle key
    let currentIndex = blockNewElementButtons.findIndex((el) => {
        return (el.dataset.collapseToggle === key);
    });

    // Slice new array starting after index
    if (currentIndex !== -1) {
        let hideNewElements = blockNewElementButtons.slice(currentIndex + 1);

        // Toggle d-none class
        hideNewElements.forEach(el => {
            el.classList.toggle(hideClass);
        });
    }
}

document.addEventListener("click", collapseToggle, false);
document.addEventListener("click", autoCollapse, false);
