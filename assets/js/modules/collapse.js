/**
 * Collapse Toggle
 * @param {Event} event
 */
const collapseToggle = function(event) {
    if (event.target.dataset.collapse === 'toggle') {
        let collapseTarget = event.target.closest(`[data-collapse="parent"]`).querySelector(`[data-collapse="target"]`);

        if (collapseTarget) {
            collapseTarget.classList.toggle("collapsed");
        }
    }
}

export { collapseToggle };