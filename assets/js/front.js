/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright 2018 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */

 /**
 * Piton Front End JS
 */

import { pitonConfig } from './modules/config.js';
import { postXHRPromise } from "./modules/xhrPromise.js";

// Set the contact honeypot to a known value
const honeypotValue = 'alt@example.com';
document.querySelector(`input[name="alt-email"]`)?.setAttribute("value", honeypotValue);

// Get reference to hidden response message element
const contactResponseMessage = document.querySelector(`[data-contact-response="true"]`);

/**
 * Contact Submit Message Request
 *
 * @param {Event} event
 */
const contactSubmitMessage = function(event) {
  if (!(event.target.dataset.contactForm === "true")) return;
  event.preventDefault();

  // Check honeypot if available
  if (event.target.querySelector(".alt-email") && event.target.querySelector(".alt-email").value !== honeypotValue) return;

  // Set indicator of work in progress
  let buttonText = (event.target.dataset.contactFormButtonText) ? event.target.dataset.contactFormButtonText : "Sending...";
  event.target.querySelector(`button[type="submit"]`).innerHTML = buttonText;

  // Make XHR request
  postXHRPromise(pitonConfig.routes.submitMessage, new FormData(event.target))
    .then(text => {
      // Success, remove form and display response message
      event.target.remove();
      if (contactResponseMessage) {
        contactResponseMessage.hidden = false;
      }
    })
    .catch(error => {
      // Error, replace form with server error message
      event.target.innerHTML = `<p>${error}</p>`;
    });
}

// Define event listeners
document.addEventListener("submit", contactSubmitMessage, false);
