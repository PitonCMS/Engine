parcelRequire=function(e,r,t,n){var i,o="function"==typeof parcelRequire&&parcelRequire,u="function"==typeof require&&require;function f(t,n){if(!r[t]){if(!e[t]){var i="function"==typeof parcelRequire&&parcelRequire;if(!n&&i)return i(t,!0);if(o)return o(t,!0);if(u&&"string"==typeof t)return u(t);var c=new Error("Cannot find module '"+t+"'");throw c.code="MODULE_NOT_FOUND",c}p.resolve=function(r){return e[t][1][r]||r},p.cache={};var l=r[t]=new f.Module(t);e[t][0].call(l.exports,p,l,l.exports,this)}return r[t].exports;function p(e){return f(p.resolve(e))}}f.isParcelRequire=!0,f.Module=function(e){this.id=e,this.bundle=f,this.exports={}},f.modules=e,f.cache=r,f.parent=o,f.register=function(r,t){e[r]=[function(e,r){r.exports=t},{}]};for(var c=0;c<t.length;c++)try{f(t[c])}catch(e){i||(i=e)}if(t.length){var l=f(t[t.length-1]);"object"==typeof exports&&"undefined"!=typeof module?module.exports=l:"function"==typeof define&&define.amd?define(function(){return l}):n&&(this[n]=l)}if(parcelRequire=f,i)throw i;return f}({"ncrz":[function(require,module,exports) {
"use strict";Object.defineProperty(exports,"__esModule",{value:!0}),exports.disableFormControl=exports.enableFormControl=void 0;var e=function(e){e&&e.disabled&&(e.disabled=!1)};exports.enableFormControl=e;var o=function(e){e&&!e.disabled&&(e.disabled=!0)};exports.disableFormControl=o;
},{}],"Jvqk":[function(require,module,exports) {
"use strict";function e(t){return(e="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e})(t)}Object.defineProperty(exports,"__esModule",{value:!0}),exports.dismissAlertInlineMessage=exports.alertInlineMessage=void 0;var t=document.querySelector('[data-alert-modal="1"]'),r=function(e){"alert"===e.target.dataset.dismiss&&e.target.closest('[data-alert="container"]').remove()};exports.dismissAlertInlineMessage=r;var n=function(r,n,o){var a=document.createElement("div");a.innerHTML=pitonConfig.alertInlineHTML,a.querySelector('[data-alert="container"]').classList.add("alert-"+r),a.querySelector('[data-alert="heading"]').innerHTML=n,"object"===e(o)?o=o.join("<br>"):void 0===o&&(o=""),a.querySelector('[data-alert="content"]').innerHTML=o,t.insertAdjacentHTML("afterbegin",a.innerHTML),window.scrollTo(0,0)};exports.alertInlineMessage=n;
},{}],"BLPW":[function(require,module,exports) {
"use strict";Object.defineProperty(exports,"__esModule",{value:!0}),exports.collapseToggle=void 0;var e=function(e){if("toggle"===e.target.dataset.collapse){var t=e.target.closest('[data-collapse="parent"]').querySelector('[data-collapse="target"]');t&&t.classList.toggle("collapsed")}};exports.collapseToggle=e;
},{}],"mlrC":[function(require,module,exports) {
"use strict";var e=require("./formControl.js"),t=require("./alert.js"),n=require("./collapse.js");document.querySelectorAll("form").forEach(function(t){var n=t.querySelectorAll('[data-form-button="save"]');n&&(n.forEach(function(t){(0,e.disableFormControl)(t)}),t.addEventListener("input",function(t){n.forEach(function(t){(0,e.enableFormControl)(t)})},!1)),t.querySelectorAll('[data-form-button="cancel"]').forEach(function(e){e.addEventListener("click",function(e){confirm("Click Ok to discard your changes, or cancel continue editing?")||e.preventDefault()},!1)}),t.querySelectorAll("[data-delete-prompt]").forEach(function(e){e.addEventListener("click",function(e){confirm(e.target.dataset.deletePrompt)||e.preventDefault()},!1)})}),document.addEventListener("click",t.dismissAlertInlineMessage,!1),document.addEventListener("click",n.collapseToggle,!1);
},{"./formControl.js":"ncrz","./alert.js":"Jvqk","./collapse.js":"BLPW"}],"iiz7":[function(require,module,exports) {
"use strict";Object.defineProperty(exports,"__esModule",{value:!0}),exports.disableSpinner=exports.enableSpinner=void 0;var e=function(){document.querySelector("body > .spinner").classList.contains("d-none")&&document.querySelector("body > .spinner").classList.remove("d-none")};exports.enableSpinner=e;var n=function(){document.querySelector("body > .spinner").classList.contains("d-none")||document.querySelector("body > .spinner").classList.add("d-none")};exports.disableSpinner=n;
},{}],"ZwDW":[function(require,module,exports) {
"use strict";Object.defineProperty(exports,"__esModule",{value:!0}),exports.postXHRPromise=exports.getXHRPromise=void 0;var e=require("./alert.js"),t=require("./spinner.js");function r(e,t){return s(e)||i(e,t)||a(e,t)||n()}function n(){throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}function a(e,t){if(e){if("string"==typeof e)return o(e,t);var r=Object.prototype.toString.call(e).slice(8,-1);return"Object"===r&&e.constructor&&(r=e.constructor.name),"Map"===r||"Set"===r?Array.from(e):"Arguments"===r||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(r)?o(e,t):void 0}}function o(e,t){(null==t||t>e.length)&&(t=e.length);for(var r=0,n=new Array(t);r<t;r++)n[r]=e[r];return n}function i(e,t){if("undefined"!=typeof Symbol&&Symbol.iterator in Object(e)){var r=[],n=!0,a=!1,o=void 0;try{for(var i,s=e[Symbol.iterator]();!(n=(i=s.next()).done)&&(r.push(i.value),!t||r.length!==t);n=!0);}catch(u){a=!0,o=u}finally{try{n||null==s.return||s.return()}finally{if(a)throw o}}return r}}function s(e){if(Array.isArray(e))return e}var u=function(r,n,a){var o=new XMLHttpRequest;return new Promise(function(i,s){var u;o.onreadystatechange=function(){if(o.readyState===XMLHttpRequest.DONE)try{200===o.status?"success"===(u=JSON.parse(o.responseText)).status?(i(u.text),(0,t.disableSpinner)()):(s(),(0,e.alertInlineMessage)("danger","Piton Error",[u.text]),(0,t.disableSpinner)()):(s(u.text),(0,e.alertInlineMessage)("danger","Server Error "+u.status,[u.text]),(0,t.disableSpinner)())}catch(r){s(r),(0,e.alertInlineMessage)("danger","Exception",[r]),(0,t.disableSpinner)()}},o.open(r,n,!0),o.setRequestHeader("X-Requested-With","XMLHttpRequest"),o.send(a)})},c=function(e,t){if(t){var n;if(t instanceof URLSearchParams)n=t;else{n=new URLSearchParams;for(var a=0,o=Object.entries(t);a<o.length;a++){var i=r(o[a],2),s=i[0],c=i[1];n.append(s,c)}}e+="?"+n.toString()}return u("GET",e)};exports.getXHRPromise=c;var l=function(e,t){var n;if(t instanceof FormData)n=t;else{n=new FormData;for(var a=0,o=Object.entries(t);a<o.length;a++){var i=r(o[a],2),s=i[0],c=i[1];n.append(s,c)}}return n.append(pitonConfig.csrfTokenName,pitonConfig.csrfTokenValue),u("POST",e,n)};exports.postXHRPromise=l;
},{"./alert.js":"Jvqk","./spinner.js":"iiz7"}],"FHcH":[function(require,module,exports) {
"use strict";Object.defineProperty(exports,"__esModule",{value:!0}),exports.applyFilters=exports.setFilterPath=void 0;var e,t=require("./spinner.js"),n=require("./xhrPromise.js"),r=document.querySelector('[data-filter="content"]'),i=function(){if(r)for(;r.firstChild;)r.removeChild(r.lastChild)},a=function(e){"clear"===e.target.dataset.filterControl&&e.target.closest('[data-filter="options"]').querySelectorAll("input").forEach(function(e){e.checked=!1})},o=function(t){e&&r&&"apply"===t.target.dataset.filterControl&&c()},c=function(){var a=document.querySelectorAll('[data-filter="options"] input'),o={};(0,t.enableSpinner)(),a.forEach(function(e){e.checked&&(o.hasOwnProperty(e.name)?o[e.name]+=","+e.value:o[e.name]=e.value)}),(0,n.getXHRPromise)(e,o).then(function(e){return i(),e}).then(function(e){r.insertAdjacentHTML("afterbegin",e)}).then(function(){(0,t.disableSpinner)()})};exports.applyFilters=c;var l=function(t){e=t};exports.setFilterPath=l;var s=function(a){if(a.target.closest(".pagination > div")){a.preventDefault(),(0,t.enableSpinner)();var o=a.target.closest(".pagination > div").querySelector("a").href,c=new URL(o),l=new URLSearchParams(c.search);(0,n.getXHRPromise)(e,l).then(function(e){return i(),e}).then(function(e){r.insertAdjacentHTML("afterbegin",e)}).then(function(){(0,t.disableSpinner)()})}};document.addEventListener("click",o,!1),document.addEventListener("click",a,!1),document.addEventListener("click",s,!1);
},{"./spinner.js":"iiz7","./xhrPromise.js":"ZwDW"}],"EXYK":[function(require,module,exports) {
"use strict";require("./modules/main.js");var e=require("./modules/filter.js"),t=require("./modules/xhrPromise.js"),s=require("./modules/spinner.js");(0,e.setFilterPath)(pitonConfig.routes.adminMessageGet);var a=document.querySelector('[data-message="count"]'),r=function(){(0,t.getXHRPromise)(pitonConfig.routes.adminMessageCountGet).then(function(e){a.innerHTML=e})},n=function(a){if(a.target.dataset.messageControl){var n={messageId:a.target.closest('[data-message="parent"]').dataset.messageId};if("delete"===a.target.dataset.messageControl){if(!confirm(a.target.dataset.messageDeletePrompt))return;n.control="delete"}else"archive"===a.target.dataset.messageControl?n.control="archive":"read"===a.target.dataset.messageControl&&(n.control="read");(0,s.enableSpinner)(),(0,t.postXHRPromise)(pitonConfig.routes.adminMessageSave,n).then(function(){(0,e.applyFilters)(),r()}).then(function(){(0,s.disableSpinner)()})}};document.addEventListener("click",n,!1);
},{"./modules/main.js":"mlrC","./modules/filter.js":"FHcH","./modules/xhrPromise.js":"ZwDW","./modules/spinner.js":"iiz7"}]},{},["EXYK"], null)
//# sourceMappingURL=/messages.js.map