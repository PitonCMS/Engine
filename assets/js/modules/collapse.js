// Class name to toggle to show/hide elements
const collapseClass = "collapsed";

/**
 * Collapse Toggle
 * @param {Event} event
 */
const collapseToggle = function(event) {
    if (!event.target.closest(`[data-collapse-toggle]`)) return;
    let toggleKey = event.target.closest(`[data-collapse-toggle]`).dataset.collapseToggle;

    // Find the matching collapse target by value and toggle class
    let collapseTarget = document.querySelector(`[data-collapse-target="${toggleKey}"]`);
    collapseTarget.classList.toggle(collapseClass);
}

/**
 * Auto Collapse
 * @param {Event} event
 */
const autoCollapse = function(event) {
    if (!event.target.closest(`[data-collapse-auto]`)) return;

    let collapseTarget = event.target.closest(`[data-collapse-auto]`);
    collapseTarget.classList.toggle(collapseClass);
}

document.addEventListener("click", collapseToggle, false);
document.addEventListener("click", autoCollapse, false);
