import { alertInlineMessage } from './alert.js';
import { disableSpinner } from './spinner.js';

/**
 * XHR Request Promise
 * @param {string} method "GET"|"POST"
 * @param {string} url    Resource request URL
 * @param {mixed}  data   String or object
 */
const XHRPromise = function(method, url, data) {
    let xhr = new XMLHttpRequest();

    return new Promise((resolve, reject) => {
        xhr.onreadystatechange = () => {
            if (xhr.readyState !== XMLHttpRequest.DONE) return;

            try {
                if (xhr.status === 200) {
                    // Successful server response
                    let response = JSON.parse(xhr.responseText);
                    if (response.status === "success") {
                        // Response content successful
                        resolve(response.text);
                        disableSpinner();
                    } else {
                        // Response successful but application failed
                        reject(alertInlineMessage('danger', 'Failed', [response.text]));
                        disableSpinner();
                    }
                } else {
                    // Failed server runtime response
                    reject(alertInlineMessage('danger', 'Failed', [response.text]));
                    disableSpinner();
                }
            } catch (error) {
                reject(alertInlineMessage('danger', 'Error', [error]));
                disableSpinner();
            }
        }

        // Setup and send
        xhr.open(method, url, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.send(data);
    });
}

/**
 * GET XHR Promise Request
 * @param {string} url    Resource URL
 * @param {string} params Query string parameters
 */
const getXHRPromise = function(url, params) {
    let queryString;

    // Accept query string or build from object
    if (typeof params === 'string') {
        queryString = params;
    } else if (typeof params === 'object') {
        queryString = Object.keys(params).map((k) => {
            return encodeURIComponent(k) + '=' + encodeURIComponent(params[k])
        }).join('&');
    }

    // Attach query string to URL
    if (queryString) {
        url += '?' + queryString;
    }

    return XHRPromise("GET", url);
}

/**
 * POST XHR Promise Request
 * @param {string} url  Resource URL
 * @param {string} data FormData object or object key value pairs
 */
const postXHRPromise = function(url, data) {
    data = data ?? {};
    data[pitonConfig.csrfTokenName] = pitonConfig.csrfTokenValue

    // Serialize data
    let postData = Object.keys(data).map((k) => {
        return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
    }).join('&');

    return XHRPromise("POST", url,  postData);
}

export { getXHRPromise, postXHRPromise };
