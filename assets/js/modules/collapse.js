/**
 * Collapse Toggle
 * @param {Event} event
 */
const collapseToggle = function(event) {
    if (!event.target.closest(`[data-collapse-toggle]`)) return;
    let toggleKey = event.target.closest(`[data-collapse-toggle]`).dataset.collapseToggle;

    // Find the matching collapse target by value and toggle class
    document.querySelector(`[data-collapse-target="${toggleKey}"]`)?.classList.toggle("collapsed");
}

document.addEventListener("click", collapseToggle, false);
