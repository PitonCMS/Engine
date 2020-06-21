import { alertInlineMessage } from './alert.js';
import { disableSpinner } from './spinner.js';

/**
 * XHR Request Promise
 * @param {string} method "GET"|"POST"
 * @param {string} url    Resource request URL
 * @param {FormData} data   FormData payload to send
 */
const XHRPromise = function(method, url, data) {
    let xhr = new XMLHttpRequest();

    return new Promise((resolve, reject) => {

        let response;
        xhr.onreadystatechange = () => {
            if (xhr.readyState !== XMLHttpRequest.DONE) return;

            try {
                if (xhr.status === 200) {
                    // Successful server response
                    response = JSON.parse(xhr.responseText);

                    if (response.status === "success") {
                        // Response successful, resolve
                        resolve(response.text);
                        disableSpinner();
                    } else {
                        // Response successful but application failed, reject and alert
                        reject();
                        alertInlineMessage('danger', 'Piton Error', [response.text]);
                        disableSpinner();
                    }
                } else {
                    // Failed server runtime response
                    reject(response.text);
                    alertInlineMessage('danger', 'Server Error ' + response.status, [response.text]);
                    disableSpinner();
                }
            } catch (error) {
                // JS Error thrown
                reject(error);
                alertInlineMessage('danger', 'Exception', [error]);
                disableSpinner();
            }
        }

        // Setup and send
        xhr.open(method, url, true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.send(data);
    });
}

/**
 * GET XHR Promise Request
 * @param {string} url  Resource URL
 * @param {object} data Object with query string parameters as key: values
 */
const getXHRPromise = function(url, data) {

    // Create query string if a data object was provided
    if (data) {
        let queryString = new URLSearchParams();
        for (let [key, value] of Object.entries(data)) {
            queryString.append(key, value);
        }
        url += "?" + queryString.toString();
    }

    return XHRPromise("GET", url);
}

/**
 * POST XHR Promise Request
 * @param {string} url  Resource URL
 * @param {object} data Object with key: values, or FormData instance
 */
const postXHRPromise = function(url, data) {
    let formData;
    if (data instanceof FormData) {
        formData = data;
    } else {
        formData = new FormData();
        for (let [key, value] of Object.entries(data)) {
            formData.append(key, value);
        }
    }

    formData.append(pitonConfig.csrfTokenName, pitonConfig.csrfTokenValue);

    return XHRPromise("POST", url,  formData);
}

export { getXHRPromise, postXHRPromise };
