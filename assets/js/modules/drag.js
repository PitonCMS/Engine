// Reference to element to be moved
let movedElement;

// Event to dispatch pseudo "input" event
const inputEvent = new Event("input", {"bubbles": true});

// Drop zone classes
const dragZone = "border: 1px dashed #000; height: 20px;";
const dragHover = "border: 1px dashed #000; height: 60px;";

// Empty drop zone divs to inject in DOM around draggable elements
const dropZone = document.createElement("div");
dropZone.classList.add("drag-drop");
dropZone.style.cssText = dragZone;

/**
 * Drag Start Handler
 * @param {Event} event
 */
const dragStartHandler = function(event) {
    // Save reference to the element being moved
    movedElement = event.target;

    event.dataTransfer.setData("text/plain", null);
    event.dataTransfer.dropEffect = "move";

    // Add drop zone divs between each child
    // setTimeout() hack: https://stackoverflow.com/a/34698388/452133
    // To allow DOM manipulation in dragstart
    setTimeout(() => {
        Array.from(this.children).forEach(element => {
            this.insertBefore(dropZone.cloneNode(), element);
        });

        this.appendChild(dropZone);
    }, 0);

}

/**
 * Drag Enter Handler
 * @param {Event} event
 */
const dragEnterHandler = function(event) {
    event.preventDefault();
    event.stopPropagation();
    event.dataTransfer.dropEffect = "move";

    if (event.target.classList && event.target.classList.contains('drag-drop')) {
        event.target.classList.add('drag-hover');
        event.target.style.cssText = dragHover;
    }
}

/**
 * Drag Over Handler
 * @param {Event} event
 */
const dragOverHandler = function(event) {
    event.preventDefault();
    event.stopPropagation();
    event.dataTransfer.dropEffect = "move";
}

/**
 * Drag Leave Handler
 * @param {Event} event
 */
const dragLeaveHandler = function(event) {
    event.preventDefault();
    event.stopPropagation();
    event.dataTransfer.dropEffect = "move";

    if (event.target.classList && event.target.classList.contains('drag-drop')) {
        event.target.classList.remove('drag-hover');
        event.target.style.cssText = dragZone;
    }
}

/**
 * Drag Drop Handler
 * @param {Event} event
 */
const dragDropHandler = function(event) {
    event.preventDefault();
    event.stopPropagation();

    if (movedElement !== event.target && event.target.classList && event.target.classList.contains('drag-drop')) {
        this.insertBefore(movedElement, event.target.nextSibling)
        this.dispatchEvent(inputEvent);
    }
}

/**
 * Drag End Handler
 * @param {Event} event
 */
const dragEndHandler = function(event) {
    // Cleanup drop zones
    this.querySelectorAll(".drag-drop").forEach(zone => {
        zone.remove();
    });
}

export { dragStartHandler, dragEnterHandler, dragOverHandler, dragLeaveHandler, dragDropHandler, dragEndHandler };