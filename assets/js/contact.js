// --------------------------------------------------------
// Front End Contact Form Submission
// --------------------------------------------------------
import { postXHRPromise } from './modules/xhrPromise.js';

const contactSubmitMessage = function(event) {
  if (!(event.target.type === "button" && event.target.closest(`[data-contact-form="true"]`))) return;
    event.preventDefault();

    let form = event.target.closest("form");

    // Check honeypot
    if (form.querySelector(".alt-email").value !== 'alt@example.com') return;

    if (form) {
      // Set indicator of work in progress
      let buttonText = (form.dataset.contactFormButtonText) ? form.dataset.contactFormButtonText : "Sending...";
      form.querySelector(`button[type="button"]`).innerHTML = buttonText;

      postXHRPromise(pitonConfig.routes.submitMessage, new FormData(form))
        .then(text => {
            form.innerHTML = `<p>${text}</p>`;
        })
        .catch(error => {
            form.innerHTML = `<p>${error}</p>`;
        });
    }
}

document.addEventListener("click", contactSubmitMessage, false);
