parcelRequire=function(e,r,t,n){var i,o="function"==typeof parcelRequire&&parcelRequire,u="function"==typeof require&&require;function f(t,n){if(!r[t]){if(!e[t]){var i="function"==typeof parcelRequire&&parcelRequire;if(!n&&i)return i(t,!0);if(o)return o(t,!0);if(u&&"string"==typeof t)return u(t);var c=new Error("Cannot find module '"+t+"'");throw c.code="MODULE_NOT_FOUND",c}p.resolve=function(r){return e[t][1][r]||r},p.cache={};var l=r[t]=new f.Module(t);e[t][0].call(l.exports,p,l,l.exports,this)}return r[t].exports;function p(e){return f(p.resolve(e))}}f.isParcelRequire=!0,f.Module=function(e){this.id=e,this.bundle=f,this.exports={}},f.modules=e,f.cache=r,f.parent=o,f.register=function(r,t){e[r]=[function(e,r){r.exports=t},{}]};for(var c=0;c<t.length;c++)try{f(t[c])}catch(e){i||(i=e)}if(t.length){var l=f(t[t.length-1]);"object"==typeof exports&&"undefined"!=typeof module?module.exports=l:"function"==typeof define&&define.amd?define(function(){return l}):n&&(this[n]=l)}if(parcelRequire=f,i)throw i;return f}({"ncrz":[function(require,module,exports) {
var t=function(t){if(t.target.closest("form")){var e=t.target.closest("form").querySelectorAll('[data-form-button="save"], [data-form-button="cancel"]');e&&e.forEach(function(t){t.disabled=!1})}},e=function(t){if("cancel"===t.target.dataset.formButton)if(t.stopPropagation(),confirm("Click Ok to discard your changes, or Cancel continue editing.")){var e=t.target.closest("form").querySelectorAll('[data-form-button="save"], [data-form-button="cancel"]');e&&(t.target.dataset.formResetHref?(t.preventDefault(),window.location=t.target.dataset.formResetHref):setTimeout(function(){e.forEach(function(t){t.disabled=!0})},0))}else t.preventDefault()},a=function(t){t.target.dataset.deletePrompt&&(confirm(t.target.dataset.deletePrompt)||t.preventDefault())};document.addEventListener("input",t,!1),document.addEventListener("click",a,!1),document.addEventListener("click",e,!1);
},{}],"BLPW":[function(require,module,exports) {
var t="collapsed",a=function(a){if(a.target.closest("[data-collapse-toggle]")){var e=a.target.closest("[data-collapse-toggle]").dataset.collapseToggle;document.querySelector('[data-collapse-target="'.concat(e,'"]')).classList.toggle(t)}},e=function(a){if(a.target.closest("[data-collapse-auto]")){var e=a.target.closest("[data-collapse-auto]").dataset.collapseAuto;document.querySelector('[data-collapse-target="'.concat(e,'"]')).classList.add(t)}};document.addEventListener("click",a,!1),document.addEventListener("click",e,!1);
},{}],"Jvqk":[function(require,module,exports) {
"use strict";function e(t){return(e="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e})(t)}Object.defineProperty(exports,"__esModule",{value:!0}),exports.alertInlineMessage=void 0;var t=document.querySelector('[data-alert-modal="1"]'),r=function(e){var t;"alert"===e.target.dataset.dismiss&&(null===(t=e.target.closest('[data-alert="container"]'))||void 0===t||t.remove())},n=function(r,n,o){var a=document.createElement("div");a.innerHTML=pitonConfig.alertInlineHTML,a.querySelector('[data-alert="container"]').classList.add("alert-"+r),a.querySelector('[data-alert="heading"]').innerHTML=n,o=Array.isArray(o)&&null!==o?o.join("<br>"):o instanceof Error?o.message:"object"===e(o)&&null!==o?Object.values(o).join("<br>"):String(o),a.querySelector('[data-alert="content"]').innerHTML=o,t?(t.insertAdjacentHTML("afterbegin",a.innerHTML),window.scrollTo(0,0)):alert(a.innerHTML)};exports.alertInlineMessage=n,document.addEventListener("click",r,!1);
},{}],"mlrC":[function(require,module,exports) {
"use strict";require("./formControl.js"),require("./collapse.js"),require("./alert.js");
},{"./formControl.js":"ncrz","./collapse.js":"BLPW","./alert.js":"Jvqk"}],"iiz7":[function(require,module,exports) {
"use strict";Object.defineProperty(exports,"__esModule",{value:!0}),exports.disableSpinner=exports.enableSpinner=void 0;var e=function(){document.body.insertAdjacentHTML("afterbegin",pitonConfig.spinnerHTML)};exports.enableSpinner=e;var n=function(){var e;null===(e=document.querySelector('[data-spinner="true"]'))||void 0===e||e.remove()};exports.disableSpinner=n;
},{}],"ZwDW":[function(require,module,exports) {
"use strict";function t(t,n){return a(t)||o(t,n)||r(t,n)||e()}function e(){throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}function r(t,e){if(t){if("string"==typeof t)return n(t,e);var r=Object.prototype.toString.call(t).slice(8,-1);return"Object"===r&&t.constructor&&(r=t.constructor.name),"Map"===r||"Set"===r?Array.from(t):"Arguments"===r||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(r)?n(t,e):void 0}}function n(t,e){(null==e||e>t.length)&&(e=t.length);for(var r=0,n=new Array(e);r<e;r++)n[r]=t[r];return n}function o(t,e){if("undefined"!=typeof Symbol&&Symbol.iterator in Object(t)){var r=[],n=!0,o=!1,a=void 0;try{for(var i,s=t[Symbol.iterator]();!(n=(i=s.next()).done)&&(r.push(i.value),!e||r.length!==e);n=!0);}catch(c){o=!0,a=c}finally{try{n||null==s.return||s.return()}finally{if(o)throw a}}return r}}function a(t){if(Array.isArray(t))return t}Object.defineProperty(exports,"__esModule",{value:!0}),exports.postXHRPromise=exports.getXHRPromise=void 0;var i=function(t,e,r){var n=new XMLHttpRequest;return new Promise(function(o,a){var i;n.onreadystatechange=function(){if(n.readyState===XMLHttpRequest.DONE)try{if(200===n.status){if("success"===(i=JSON.parse(n.responseText)).status)return void o(i.text);throw new Error("Application Error ".concat(i.text,"."))}throw new Error("Server Error ".concat(n.status," ").concat(n.statusText,"."))}catch(e){if(!(e instanceof Error))var t=new Error(t);a(e.message)}},n.open(t,e,!0),n.setRequestHeader("X-Requested-With","XMLHttpRequest"),n.send(r)})},s=function(e,r){if(r){var n;if(r instanceof URLSearchParams)n=r;else{n=new URLSearchParams;for(var o=0,a=Object.entries(r);o<a.length;o++){var s=t(a[o],2),c=s[0],u=s[1];n.append(c,u)}}e+="?"+n.toString()}return i("GET",e)};exports.getXHRPromise=s;var c=function(e,r){var n;if(r instanceof FormData)n=r;else{n=new FormData;for(var o=0,a=Object.entries(r);o<a.length;o++){var s=t(a[o],2),c=s[0],u=s[1];n.append(c,u)}}return n.append(pitonConfig.csrfTokenName,pitonConfig.csrfTokenValue),i("POST",e,n)};exports.postXHRPromise=c;
},{}],"IYwL":[function(require,module,exports) {
"use strict";require("./modules/main.js");var e=require("./modules/spinner.js"),t=require("./modules/xhrPromise.js"),a=require("./modules/alert.js"),r=document.querySelector('[data-media-category="spare"] > div');r.classList.add("new-category");var n=0,o=function(){var e=r.cloneNode(!0),t=n+++"n";e.querySelectorAll("input[name^=category]").forEach(function(e){e.name=e.name.replace(/(.+?\[)(\].+)/,"$1"+t+"$2")}),document.querySelector('[data-category="wrapper"]').appendChild(e)},d=function(r){if(r.target.dataset.deleteCategoryPrompt&&confirm(r.target.dataset.deleteCategoryPrompt)){var n=parseInt(r.target.dataset.categoryId),o=r.target.closest('[data-category="parent"]');isNaN(n)?o.remove():((0,e.enableSpinner)(),(0,t.postXHRPromise)(pitonConfig.routes.adminMediaCategoryDelete,{categoryId:n}).then(function(){o.remove()}).then(function(){(0,e.disableSpinner)()}).catch(function(t){(0,e.disableSpinner)(),(0,a.alertInlineMessage)("danger","Failed to Delete Category",t)}))}};document.querySelector('[data-category="add"]').addEventListener("click",o,!1),document.addEventListener("click",d,!1);
},{"./modules/main.js":"mlrC","./modules/spinner.js":"iiz7","./modules/xhrPromise.js":"ZwDW","./modules/alert.js":"Jvqk"}]},{},["IYwL"], null)