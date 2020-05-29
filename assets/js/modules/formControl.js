/**
 * Enable Form Control
 *
 * @param {object} control Control button element
 */
const enableFormControl = function(control) {
    if (control && control.disabled) {
        control.disabled = false;
    }
}

/**
 * Disable Form Control
 *
 * @param {object} control Control button element
 */
const disableFormControl =  function(control) {
    if (control && !control.disabled) {
        control.disabled = true;
    }
}

export { enableFormControl, disableFormControl };