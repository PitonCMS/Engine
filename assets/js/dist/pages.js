parcelRequire=function(e,r,t,n){var i,o="function"==typeof parcelRequire&&parcelRequire,u="function"==typeof require&&require;function f(t,n){if(!r[t]){if(!e[t]){var i="function"==typeof parcelRequire&&parcelRequire;if(!n&&i)return i(t,!0);if(o)return o(t,!0);if(u&&"string"==typeof t)return u(t);var c=new Error("Cannot find module '"+t+"'");throw c.code="MODULE_NOT_FOUND",c}p.resolve=function(r){return e[t][1][r]||r},p.cache={};var l=r[t]=new f.Module(t);e[t][0].call(l.exports,p,l,l.exports,this)}return r[t].exports;function p(e){return f(p.resolve(e))}}f.isParcelRequire=!0,f.Module=function(e){this.id=e,this.bundle=f,this.exports={}},f.modules=e,f.cache=r,f.parent=o,f.register=function(r,t){e[r]=[function(e,r){r.exports=t},{}]};for(var c=0;c<t.length;c++)try{f(t[c])}catch(e){i||(i=e)}if(t.length){var l=f(t[t.length-1]);"object"==typeof exports&&"undefined"!=typeof module?module.exports=l:"function"==typeof define&&define.amd?define(function(){return l}):n&&(this[n]=l)}if(parcelRequire=f,i)throw i;return f}({"ncrz":[function(require,module,exports) {
document.querySelectorAll("form").forEach(function(e){var t=e.querySelectorAll('[data-form-button="save"], [data-form-button="cancel"]');t&&(t.forEach(function(e){e.disabled=!0}),e.addEventListener("input",function(e){t.forEach(function(e){e.disabled=!1})},!1)),e.querySelectorAll('[data-form-button="cancel"]').forEach(function(e){e.addEventListener("click",function(e){confirm("Click Ok to discard your changes, or Cancel continue editing.")?e.target.dataset.formResetHref&&(e.preventDefault(),window.location=e.target.dataset.formResetHref):e.preventDefault()},!1)}),e.querySelectorAll("[data-delete-prompt]").forEach(function(e){e.addEventListener("click",function(e){confirm(e.target.dataset.deletePrompt)||e.preventDefault()},!1)})});
},{}],"Jvqk":[function(require,module,exports) {
"use strict";Object.defineProperty(exports,"__esModule",{value:!0}),exports.dismissAlertInlineMessage=exports.alertInlineMessage=void 0;var e=document.querySelector('[data-alert-modal="1"]'),t=function(e){var t;"alert"===e.target.dataset.dismiss&&(null===(t=e.target.closest('[data-alert="container"]'))||void 0===t||t.remove())};exports.dismissAlertInlineMessage=t;var r=function(t,r,n){var a,i=document.createElement("div");(i.innerHTML=pitonConfig.alertInlineHTML,i.querySelector('[data-alert="container"]').classList.add("alert-"+t),i.querySelector('[data-alert="heading"]').innerHTML=r,void 0===n||"string"==typeof n)&&(n=[null!==(a=n)&&void 0!==a?a:""]);n=n.join("<br>"),i.querySelector('[data-alert="content"]').innerHTML=n,e.insertAdjacentHTML("afterbegin",i.innerHTML),window.scrollTo(0,0)};exports.alertInlineMessage=r;
},{}],"BLPW":[function(require,module,exports) {
"use strict";Object.defineProperty(exports,"__esModule",{value:!0}),exports.collapseToggle=void 0;var e=function(e){if("toggle"===e.target.dataset.collapse){var t=e.target.closest('[data-collapse="parent"]').querySelector('[data-collapse="target"]');t&&t.classList.toggle("collapsed")}};exports.collapseToggle=e;
},{}],"mlrC":[function(require,module,exports) {
"use strict";require("./formControl.js");var e=require("./alert.js"),r=require("./collapse.js");document.addEventListener("click",e.dismissAlertInlineMessage,!1),document.addEventListener("click",r.collapseToggle,!1);
},{"./formControl.js":"ncrz","./alert.js":"Jvqk","./collapse.js":"BLPW"}],"iiz7":[function(require,module,exports) {
"use strict";Object.defineProperty(exports,"__esModule",{value:!0}),exports.disableSpinner=exports.enableSpinner=void 0;var e=function(){document.querySelector("body > .spinner").classList.contains("d-none")&&document.querySelector("body > .spinner").classList.remove("d-none")};exports.enableSpinner=e;var n=function(){document.querySelector("body > .spinner").classList.contains("d-none")||document.querySelector("body > .spinner").classList.add("d-none")};exports.disableSpinner=n;
},{}],"ZwDW":[function(require,module,exports) {
"use strict";function t(t,n){return a(t)||o(t,n)||r(t,n)||e()}function e(){throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}function r(t,e){if(t){if("string"==typeof t)return n(t,e);var r=Object.prototype.toString.call(t).slice(8,-1);return"Object"===r&&t.constructor&&(r=t.constructor.name),"Map"===r||"Set"===r?Array.from(t):"Arguments"===r||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(r)?n(t,e):void 0}}function n(t,e){(null==e||e>t.length)&&(e=t.length);for(var r=0,n=new Array(e);r<e;r++)n[r]=t[r];return n}function o(t,e){if("undefined"!=typeof Symbol&&Symbol.iterator in Object(t)){var r=[],n=!0,o=!1,a=void 0;try{for(var i,s=t[Symbol.iterator]();!(n=(i=s.next()).done)&&(r.push(i.value),!e||r.length!==e);n=!0);}catch(c){o=!0,a=c}finally{try{n||null==s.return||s.return()}finally{if(o)throw a}}return r}}function a(t){if(Array.isArray(t))return t}Object.defineProperty(exports,"__esModule",{value:!0}),exports.postXHRPromise=exports.getXHRPromise=void 0;var i=function(t,e,r){var n=new XMLHttpRequest;return new Promise(function(o,a){var i;n.onreadystatechange=function(){if(n.readyState===XMLHttpRequest.DONE)try{if(200===n.status){if("success"===(i=JSON.parse(n.responseText)).status)return void o(i.text);throw new Error("Application Error ".concat(i.text,"."))}throw new Error("Server Error ".concat(n.status," ").concat(n.statusText,"."))}catch(e){if(!(e instanceof Error))var t=new Error(t);a(e.message)}},n.open(t,e,!0),n.setRequestHeader("X-Requested-With","XMLHttpRequest"),n.send(r)})},s=function(e,r){if(r){var n;if(r instanceof URLSearchParams)n=r;else{n=new URLSearchParams;for(var o=0,a=Object.entries(r);o<a.length;o++){var s=t(a[o],2),c=s[0],u=s[1];n.append(c,u)}}e+="?"+n.toString()}return i("GET",e)};exports.getXHRPromise=s;var c=function(e,r){var n;if(r instanceof FormData)n=r;else{n=new FormData;for(var o=0,a=Object.entries(r);o<a.length;o++){var s=t(a[o],2),c=s[0],u=s[1];n.append(c,u)}}return n.append(pitonConfig.csrfTokenName,pitonConfig.csrfTokenValue),i("POST",e,n)};exports.postXHRPromise=c;
},{}],"FHcH":[function(require,module,exports) {
"use strict";Object.defineProperty(exports,"__esModule",{value:!0}),exports.applyFilters=exports.setFilterPath=void 0;var e,t,n,r=require("./spinner.js"),i=require("./xhrPromise.js"),a=document.querySelector('[data-filter="content"]'),o=function(e){n=e};exports.setFilterPath=o;var c=function(){if(a)for(;a.firstChild;)a.removeChild(a.lastChild)},l=function(e){"clear"===e.target.dataset.filterControl&&e.target.closest('[data-filter="options"]').querySelectorAll("input").forEach(function(e){e.checked=!1})},u=function(e){n&&a&&"apply"===e.target.dataset.filterControl&&d()},s=function(e){return(0,i.getXHRPromise)(n,e).then(function(e){return c(),e}).then(function(e){a.insertAdjacentHTML("afterbegin",e)}).then(function(){(0,r.disableSpinner)()})},d=function(){var e=document.querySelectorAll('[data-filter="options"] input'),t={};return(0,r.enableSpinner)(),e.forEach(function(e){e.checked&&(t.hasOwnProperty(e.name)?t[e.name]+=","+e.value:t[e.name]=e.value)}),s(t)};exports.applyFilters=d;var f=function(){var e={terms:document.querySelector('[data-filter="search"] input').value};return(0,r.enableSpinner)(),s(e)},p=function(e){if(e.target.closest(".pagination > div")){e.preventDefault(),(0,r.enableSpinner)();var t=e.target.closest(".pagination > div").querySelector("a").href,n=new URL(t),i=new URLSearchParams(n.search);return s(i)}};document.addEventListener("click",u,!1),document.addEventListener("click",l,!1),document.addEventListener("click",p,!1),null===(e=document.querySelector('[data-filter-control="search"]'))||void 0===e||e.addEventListener("click",f,!1),null===(t=document.querySelector('[data-filter="search"] input'))||void 0===t||t.addEventListener("keypress",function(e){"Enter"===e.key&&f()},!1);
},{"./spinner.js":"iiz7","./xhrPromise.js":"ZwDW"}],"xFyR":[function(require,module,exports) {
"use strict";require("./modules/main.js");var e=require("./modules/filter.js");(0,e.setFilterPath)(pitonConfig.routes.adminPageGet);
},{"./modules/main.js":"mlrC","./modules/filter.js":"FHcH"}]},{},["xFyR"], null)