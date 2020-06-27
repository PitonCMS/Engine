// --------------------------------------------------------
// Main JS
// --------------------------------------------------------

import './formControl.js';
import { dismissAlertInlineMessage } from './alert.js';
import { collapseToggle } from './collapse.js';

// Binding click events to document
document.addEventListener("click", dismissAlertInlineMessage, false);
document.addEventListener("click", collapseToggle, false);