// --------------------------------------------------------
// Front End Contact Form Submission
// --------------------------------------------------------
import { postXHRPromise } from './modules/xhrPromise.js';

/**
 * Contact Submit Message
 * @param {Event} event
 */
const contactSubmitMessage = function(event) {
  if (!(event.target.dataset.contactForm = "true")) return;
  event.preventDefault();

  // Check honeypot if available
  if (event.target.querySelector(".alt-email") && event.target.querySelector(".alt-email").value !== 'alt@example.com') return;

  // Set indicator of work in progress
  let buttonText = (event.target.dataset.contactFormButtonText) ? event.target.dataset.contactFormButtonText : "Sending...";
  event.target.querySelector(`button[type="submit"]`).innerHTML = buttonText;

  postXHRPromise(pitonConfig.routes.submitMessage, new FormData(event.target))
    .then(text => {
      event.target.innerHTML = `<p>${text}</p>`;
    })
    .catch(error => {
      event.target.innerHTML = `<p>${error}</p>`;
    });
}

document.addEventListener("submit", contactSubmitMessage, false);
