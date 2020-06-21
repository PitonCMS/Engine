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
"use strict";Object.defineProperty(exports,"__esModule",{value:!0}),exports.postXHRPromise=exports.getXHRPromise=void 0;var e=require("./alert.js"),t=require("./spinner.js");function r(e,t){return s(e)||i(e,t)||a(e,t)||n()}function n(){throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}function a(e,t){if(e){if("string"==typeof e)return o(e,t);var r=Object.prototype.toString.call(e).slice(8,-1);return"Object"===r&&e.constructor&&(r=e.constructor.name),"Map"===r||"Set"===r?Array.from(e):"Arguments"===r||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(r)?o(e,t):void 0}}function o(e,t){(null==t||t>e.length)&&(t=e.length);for(var r=0,n=new Array(t);r<t;r++)n[r]=e[r];return n}function i(e,t){if("undefined"!=typeof Symbol&&Symbol.iterator in Object(e)){var r=[],n=!0,a=!1,o=void 0;try{for(var i,s=e[Symbol.iterator]();!(n=(i=s.next()).done)&&(r.push(i.value),!t||r.length!==t);n=!0);}catch(u){a=!0,o=u}finally{try{n||null==s.return||s.return()}finally{if(a)throw o}}return r}}function s(e){if(Array.isArray(e))return e}var u=function(r,n,a){var o=new XMLHttpRequest;return new Promise(function(i,s){var u;o.onreadystatechange=function(){if(o.readyState===XMLHttpRequest.DONE)try{200===o.status?"success"===(u=JSON.parse(o.responseText)).status?(i(u.text),(0,t.disableSpinner)()):(s(),(0,e.alertInlineMessage)("danger","Piton Error",[u.text]),(0,t.disableSpinner)()):(s(u.text),(0,e.alertInlineMessage)("danger","Server Error "+u.status,[u.text]),(0,t.disableSpinner)())}catch(r){s(r),(0,e.alertInlineMessage)("danger","Exception",[r]),(0,t.disableSpinner)()}},o.open(r,n,!0),o.setRequestHeader("X-Requested-With","XMLHttpRequest"),o.send(a)})},l=function(e,t){if(t){for(var n=new URLSearchParams,a=0,o=Object.entries(t);a<o.length;a++){var i=r(o[a],2),s=i[0],l=i[1];n.append(s,l)}e+="?"+n.toString()}return u("GET",e)};exports.getXHRPromise=l;var c=function(e,t){var n;if(t instanceof FormData)n=t;else{n=new FormData;for(var a=0,o=Object.entries(t);a<o.length;a++){var i=r(o[a],2),s=i[0],l=i[1];n.append(s,l)}}return n.append(pitonConfig.csrfTokenName,pitonConfig.csrfTokenValue),u("POST",e,n)};exports.postXHRPromise=c;
},{"./alert.js":"Jvqk","./spinner.js":"iiz7"}],"lCks":[function(require,module,exports) {
"use strict";Object.defineProperty(exports,"__esModule",{value:!0}),exports.hideModal=exports.showModalContent=exports.showModal=exports.getModal=void 0;var e=document.getElementById("modal"),o=function(){return e};exports.getModal=o;var t=function(){e.classList.remove("d-none")};exports.showModal=t;var d=function(o,t){e.querySelector('[data-modal="header"]').innerHTML=o,e.querySelector('[data-modal="body"]').innerHTML=t,e.classList.remove("d-none"),e.querySelector('[data-modal="content"]').classList.remove("d-none")};exports.showModalContent=d;var a=function(){e.classList.add("d-none"),e.querySelector('[data-modal="content"]').classList.add("d-none"),e.querySelector('[data-modal="header"]').innerHTML="",e.querySelector('[data-modal="body"]').innerHTML=""};exports.hideModal=a,e.querySelector('[data-dismiss="modal"]').addEventListener("click",function(){a()},!1),window.addEventListener("click",function(o){o.target===e&&a()},!1);
},{}],"TCz7":[function(require,module,exports) {
"use strict";Object.defineProperty(exports,"__esModule",{value:!0}),exports.mediaSelect=void 0;var e=require("./modal.js"),t=require("./xhrPromise.js"),a=new Event("input",{bubbles:!0}),i=function(i){(0,e.showModal)(),(0,t.getXHRPromise)(pitonConfig.routes.adminMediaGet).then(function(t){(0,e.showModalContent)("Select Media",t)}),(0,e.getModal)().querySelector('[data-modal="content"]').addEventListener("click",function(t){if(t.target.closest('[data-media="1"]')){var d={id:t.target.closest('[data-media="1"]').dataset.mediaId,caption:t.target.closest('[data-media="1"]').dataset.mediaCaption,filename:t.target.closest('[data-media="1"]').dataset.mediaPath},s=i.querySelector('input[name*="media_id"]'),o=i.querySelector("img");s.value=d.id,o.src=d.filename,o.alt=d.caption,o.title=d.caption,o.classList.remove("d-none"),s.dispatchEvent(a),(0,e.hideModal)()}},!1)},d=function(e){if(e&&e.target.dataset.mediaModal)i(e.target.closest('[data-media-select="1"]'));else if(e.target.dataset.mediaClear){var t=e.target.closest('[data-media-select="1"]').querySelector('input[name*="media_id"]'),d=e.target.closest('[data-media-select="1"]').querySelector("img");t.value="",d.src="",d.alt="",d.title="",d.classList.add("d-none"),t.dispatchEvent(a)}};exports.mediaSelect=d;
},{"./modal.js":"lCks","./xhrPromise.js":"ZwDW"}],"EQVZ":[function(require,module,exports) {
"use strict";Object.defineProperty(exports,"__esModule",{value:!0}),exports.unlockSlug=exports.setCleanSlug=void 0;var e=document.querySelector('[data-url-slug="target"]'),a=function(a){"home"!==e.value&&"unlock"===e.dataset.urlSlugStatus&&(a=(a=(a=(a=a.replace(/&/g,"and")).replace("'","")).replace(/[^a-z0-9]+/gi,"-")).replace(/-+$/gi,""),e.value=a)};exports.setCleanSlug=a;var t=function(a){if("home"!==e.value){if(a.target.classList&&a.target.classList.contains("fa-lock")){if(!confirm("Are you sure you want to change the URL Slug? This can impact links and search engine results."))return;a.target.classList.replace("fa-lock","fa-unlock"),e.readOnly=!1,e.dataset.urlSlugStatus="unlock"}}else alert("You cannot change the home page slug.")};exports.unlockSlug=t;
},{}],"uxT7":[function(require,module,exports) {
"use strict";var e;Object.defineProperty(exports,"__esModule",{value:!0}),exports.getMovedElement=exports.dragEndHandler=exports.dragDropHandler=exports.dragLeaveHandler=exports.dragOverHandler=exports.dragEnterHandler=exports.dragStartHandler=void 0;var r=new Event("input",{bubbles:!0}),t=document.createElement("div");t.classList.add("drag-drop");var a=function(){return e};exports.getMovedElement=a;var n=function(r){e=r.target,r.dataTransfer.setData("text/plain",null),r.dataTransfer.dropEffect="move",setTimeout(function(){document.querySelectorAll('[draggable="true"]').forEach(function(r){r===e||r===e.nextElementSibling||e.contains(r)||r.parentElement.insertBefore(t.cloneNode(),r),r===r.parentElement.lastElementChild&&r!==e&&r.parentElement.appendChild(t.cloneNode())}),document.querySelectorAll('[data-drop-zone="1"]').forEach(function(r){e.contains(r)||r.parentElement.insertBefore(t.cloneNode(),r)})},0)};exports.dragStartHandler=n;var o=function(e){e.preventDefault(),e.stopPropagation(),e.dataTransfer.dropEffect="move",e.target.matches(".drag-drop")&&e.target.classList.add("drag-hover")};exports.dragEnterHandler=o;var d=function(e){e.preventDefault(),e.stopPropagation(),e.dataTransfer.dropEffect="move"};exports.dragOverHandler=d;var l=function(e){e.preventDefault(),e.stopPropagation(),e.dataTransfer.dropEffect="move",e.target.matches(".drag-drop")&&e.target.classList.remove("drag-hover")};exports.dragLeaveHandler=l;var p=function(r){r.preventDefault(),r.stopPropagation(),e!==r.target&&r.target.matches(".drag-drop")&&r.target.parentElement.insertBefore(e,r.target.nextSibling)};exports.dragDropHandler=p;var s=function(t){document.querySelectorAll(".drag-drop").forEach(function(e){e.remove()}),e.dispatchEvent(r)};exports.dragEndHandler=s;
},{}],"UUPH":[function(require,module,exports) {
"use strict";require("./modules/main.js");var e=require("./modules/spinner.js"),t=require("./modules/xhrPromise.js"),n=require("./modules/mediaModal.js"),a=require("./modules/url.js"),r=require("./modules/drag.js"),d=function(e){if(e.target.matches('input[name^="element_title"]')){var t=e.target.value;e.target.closest('[data-element="parent"]').querySelector(".secondary-title").innerHTML=t}},l=function(e){return new SimpleMDE({element:e,forceSync:!0,promptURLs:!0,toolbar:["bold","italic","|","heading-2","heading-3","|","unordered-list","ordered-list","|","horizontal-rule","table","|","link","guide"]})};document.querySelectorAll('a[data-element="add"]').forEach(function(n){n.addEventListener("click",function(a){a.preventDefault();var r=parseInt(n.dataset.elementCountLimit)||100,d=parseInt(n.dataset.elementCount)||0;if(d>=r)alert("This Block has the maximum number of Elements allowed by the design");else{(0,e.enableSpinner)();var o={pageTemplate:document.querySelector('input[name="template"]').value,blockKey:n.dataset.blockKey};(0,t.getXHRPromise)(pitonConfig.routes.adminPageElementGet,o).then(function(e){var t=document.createElement("div"),a=document.getElementById("block-"+n.dataset.blockKey);t.innerHTML=e,n.dataset.elementCount=++d,t.querySelector('[data-element="parent"]').classList.add("new-element"),a.insertAdjacentHTML("beforeend",t.innerHTML);var r=a.lastElementChild.querySelector('textarea[data-mde="1"]');return l(r),t.querySelector('[data-element="parent"]').getAttribute("id")}).then(function(e){}).then(function(){(0,e.disableSpinner)()}).catch(function(){(0,e.disableSpinner)()})}},!1)});var o=document.querySelector('[data-page-edit="1"]');o&&o.addEventListener("click",function(n){if(n.target.dataset.deleteElementPrompt){if(!confirm(n.target.dataset.deleteElementPrompt))return;var a=parseInt(n.target.dataset.elementId),r=n.target.closest('[data-element="parent"]');isNaN(a)?r.remove():((0,e.enableSpinner)(),(0,t.postXHRPromise)(pitonConfig.routes.adminPageElementDelete,{elementId:a}).then(function(){r.remove()}).catch(function(e){console.log("Failed to delete element: ",e)}))}},!1),o&&o.addEventListener("click",function(e){if(e.target.dataset.elementEnableInput){var t=e.target.closest('[data-element="parent"]'),n=e.target.dataset.elementEnableInput;t.querySelectorAll("[data-element-input-option]").forEach(function(e){n===e.dataset.elementInputOption?(e.classList.remove("d-none"),e.classList.add("d-block")):(e.classList.add("d-none"),e.classList.remove("d-block"))})}},!1),o&&o.querySelectorAll('textarea[data-mde="1"]').forEach(function(e){l(e)}),document.querySelector('[data-url-slug="source"]').addEventListener("input",function(e){(0,a.setCleanSlug)(e.target.value)},!1),document.querySelector('[data-url-slug-lock="1"]').addEventListener("click",function(e){(0,a.unlockSlug)(e)},!1),document.addEventListener("click",n.mediaSelect,!1),document.addEventListener("change",d,!1),document.querySelectorAll('[data-draggable="children"]').forEach(function(e){e.addEventListener("dragstart",r.dragStartHandler,!1),e.addEventListener("dragenter",r.dragEnterHandler,!1),e.addEventListener("dragover",r.dragOverHandler,!1),e.addEventListener("dragleave",r.dragLeaveHandler,!1),e.addEventListener("drop",r.dragDropHandler,!1),e.addEventListener("dragend",r.dragEndHandler,!1)});
},{"./modules/main.js":"mlrC","./modules/spinner.js":"iiz7","./modules/xhrPromise.js":"ZwDW","./modules/mediaModal.js":"TCz7","./modules/url.js":"EQVZ","./modules/drag.js":"uxT7"}]},{},["UUPH"], null)
//# sourceMappingURL=/pageEdit.js.map