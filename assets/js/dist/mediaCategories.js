parcelRequire=function(e,r,t,n){var i,o="function"==typeof parcelRequire&&parcelRequire,u="function"==typeof require&&require;function f(t,n){if(!r[t]){if(!e[t]){var i="function"==typeof parcelRequire&&parcelRequire;if(!n&&i)return i(t,!0);if(o)return o(t,!0);if(u&&"string"==typeof t)return u(t);var c=new Error("Cannot find module '"+t+"'");throw c.code="MODULE_NOT_FOUND",c}p.resolve=function(r){return e[t][1][r]||r},p.cache={};var l=r[t]=new f.Module(t);e[t][0].call(l.exports,p,l,l.exports,this)}return r[t].exports;function p(e){return f(p.resolve(e))}}f.isParcelRequire=!0,f.Module=function(e){this.id=e,this.bundle=f,this.exports={}},f.modules=e,f.cache=r,f.parent=o,f.register=function(r,t){e[r]=[function(e,r){r.exports=t},{}]};for(var c=0;c<t.length;c++)try{f(t[c])}catch(e){i||(i=e)}if(t.length){var l=f(t[t.length-1]);"object"==typeof exports&&"undefined"!=typeof module?module.exports=l:"function"==typeof define&&define.amd?define(function(){return l}):n&&(this[n]=l)}if(parcelRequire=f,i)throw i;return f}({"ncrz":[function(require,module,exports) {
var t=function(t){if(t.target.closest("form")){var e=t.target.closest("form").querySelectorAll('[data-form-button="save"], [data-form-button="cancel"]');e&&e.forEach(function(t){t.disabled=!1})}},e=function(t){if("cancel"===t.target.dataset.formButton)if(t.stopPropagation(),confirm("Click Ok to discard your changes, or Cancel continue editing.")){var e=t.target.closest("form").querySelectorAll('[data-form-button="save"], [data-form-button="cancel"]');e&&(t.target.dataset.formResetHref?(t.preventDefault(),window.location=t.target.dataset.formResetHref):setTimeout(function(){e.forEach(function(t){t.disabled=!0})},0))}else t.preventDefault()},a=function(t){t.target.dataset.deletePrompt&&(confirm(t.target.dataset.deletePrompt)||t.preventDefault())};document.addEventListener("input",t,!1),document.addEventListener("click",a,!1),document.addEventListener("click",e,!1);
},{}],"BLPW":[function(require,module,exports) {
var t="collapsed",e="d-none",a=Array.from(document.querySelectorAll('[data-collapse-toggle^="newElementButton"]')),l=function(e){if(e.target.closest("[data-collapse-toggle]")){var a=e.target.closest("[data-collapse-toggle]").dataset.collapseToggle;a.match(/^newElementButton/)&&c(a),document.querySelector('[data-collapse-target="'.concat(a,'"]')).classList.toggle(t)}},o=function(e){if(e.target.closest("[data-collapse-auto]")){var a=e.target.closest("[data-collapse-auto]").dataset.collapseAuto;document.querySelector('[data-collapse-target="'.concat(a,'"]')).classList.add(t),a.match(/^newElementButton/)&&c(a)}},c=function(t){var l=a.findIndex(function(e){return e.dataset.collapseToggle===t});-1!==l&&a.slice(l+1).forEach(function(t){t.classList.toggle(e)})};document.addEventListener("click",l,!1),document.addEventListener("click",o,!1);
},{}],"GITh":[function(require,module,exports) {
"use strict";function e(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var a=Object.getOwnPropertySymbols(e);t&&(a=a.filter(function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable})),n.push.apply(n,a)}return n}function t(t){for(var a=1;a<arguments.length;a++){var i=null!=arguments[a]?arguments[a]:{};a%2?e(Object(i),!0).forEach(function(e){n(t,e,i[e])}):Object.getOwnPropertyDescriptors?Object.defineProperties(t,Object.getOwnPropertyDescriptors(i)):e(Object(i)).forEach(function(e){Object.defineProperty(t,e,Object.getOwnPropertyDescriptor(i,e))})}return t}function n(e,t,n){return t in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n,e}function a(e){return(a="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e})(e)}Object.defineProperty(exports,"__esModule",{value:!0}),exports.pitonConfig=void 0;var i={csrfTokenRequestHeader:"Piton-CSRF-Token",routes:{adminPageGet:"/admin/page/get",adminPageElementGet:"/admin/page/element/get",adminPageElementDelete:"/admin/page/element/delete",adminMessageSave:"/admin/message/save",adminMessageGet:"/admin/message/get",adminMessageCountGet:"/admin/message/getnewmessagecount",adminMedia:"/admin/media/",adminMediaSave:"/admin/media/save",adminMediaGet:"/admin/media/get/",adminMediaControlsGet:"/admin/media/getmediacontrols",adminMediaCategorySaveOrder:"/admin/media/category/saveorder",adminMediaDelete:"/admin/media/delete",adminMediaCategoryDelete:"/admin/media/category/delete",adminMediaUploadFormGet:"/admin/media/uploadform",adminMediaUploadFile:"/admin/media/upload",adminCollection:"/admin/collection/",adminNavigationDelete:"/admin/navigation/delete",submitMessage:"/submitmessage"}};exports.pitonConfig=i,"object"===("undefined"==typeof config?"undefined":a(config))&&(exports.pitonConfig=i=t(t({},i),config));
},{}],"Jvqk":[function(require,module,exports) {
"use strict";Object.defineProperty(exports,"__esModule",{value:!0}),exports.alertInlineMessage=void 0;var e=require("./config.js");function t(e){return(t="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e})(e)}var r=document.querySelector('[data-alert-modal="true"]'),n=function(e){var t;"alert"===e.target.dataset.dismiss&&(null===(t=e.target.closest('[data-alert="container"]'))||void 0===t||t.remove())},o=function(n,o,a){if(a=Array.isArray(a)&&null!==a?a.join("<br>"):a instanceof Error?a.message:"object"===t(a)&&null!==a?Object.values(a).join("<br>"):String(a),r){var l=document.createElement("div");l.innerHTML=e.pitonConfig.alertInlineHTML,l.querySelector('[data-alert="container"]').classList.add("alert-"+n),l.querySelector('[data-alert="heading"]').innerHTML=o,l.querySelector('[data-alert="content"]').innerHTML=a,r.insertAdjacentHTML("afterbegin",l.innerHTML),window.scrollTo(0,0)}else alert(a)};exports.alertInlineMessage=o,document.addEventListener("click",n,!1);
},{"./config.js":"GITh"}],"mlrC":[function(require,module,exports) {
"use strict";require("./formControl.js"),require("./collapse.js"),require("./alert.js");
},{"./formControl.js":"ncrz","./collapse.js":"BLPW","./alert.js":"Jvqk"}],"iiz7":[function(require,module,exports) {
"use strict";Object.defineProperty(exports,"__esModule",{value:!0}),exports.disableSpinner=exports.enableSpinner=void 0;var e=require("./config.js"),n=function(){document.body.insertAdjacentHTML("afterbegin",e.pitonConfig.spinnerHTML)};exports.enableSpinner=n;var r=function(){var e;null===(e=document.querySelector('[data-spinner="true"]'))||void 0===e||e.remove()};exports.disableSpinner=r;
},{"./config.js":"GITh"}],"ZwDW":[function(require,module,exports) {
"use strict";Object.defineProperty(exports,"__esModule",{value:!0}),exports.postXHRPromise=exports.getXHRPromise=void 0;var e=require("./config.js");function r(e,r){return s(e)||a(e,r)||n(e,r)||t()}function t(){throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}function n(e,r){if(e){if("string"==typeof e)return o(e,r);var t=Object.prototype.toString.call(e).slice(8,-1);return"Object"===t&&e.constructor&&(t=e.constructor.name),"Map"===t||"Set"===t?Array.from(e):"Arguments"===t||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t)?o(e,r):void 0}}function o(e,r){(null==r||r>e.length)&&(r=e.length);for(var t=0,n=new Array(r);t<r;t++)n[t]=e[t];return n}function a(e,r){var t=null==e?null:"undefined"!=typeof Symbol&&e[Symbol.iterator]||e["@@iterator"];if(null!=t){var n,o,a=[],s=!0,i=!1;try{for(t=t.call(e);!(s=(n=t.next()).done)&&(a.push(n.value),!r||a.length!==r);s=!0);}catch(u){i=!0,o=u}finally{try{s||null==t.return||t.return()}finally{if(i)throw o}}return a}}function s(e){if(Array.isArray(e))return e}var i=function(r,t,n){var o=new XMLHttpRequest;return new Promise(function(a,s){var i;o.onreadystatechange=function(){if(o.readyState===XMLHttpRequest.DONE)try{if(200===o.status){if("success"===(i=JSON.parse(o.responseText)).status)return a(i.text);throw new Error("Application Error ".concat(i.text))}throw new Error("Server Error ".concat(o.status," ").concat(o.statusText,"."))}catch(r){if(!(r instanceof Error))var e=new Error(e);return s(r.message)}},o.open(r,t,!0),o.setRequestHeader("X-Requested-With","XMLHttpRequest"),"POST"===r&&e.pitonConfig.csrfTokenValue&&o.setRequestHeader(e.pitonConfig.csrfTokenRequestHeader,e.pitonConfig.csrfTokenValue),o.send(n)})},u=function(e,t){if(t){var n;if(t instanceof URLSearchParams)n=t;else{n=new URLSearchParams;for(var o=0,a=Object.entries(t);o<a.length;o++){var s=r(a[o],2),u=s[0],c=s[1];n.append(u,c)}}e+="?"+n.toString()}return i("GET",e)};exports.getXHRPromise=u;var c=function(e,t){var n;if(t instanceof FormData)n=t;else{n=new FormData;for(var o=0,a=Object.entries(t);o<a.length;o++){var s=r(a[o],2),u=s[0],c=s[1];n.append(u,c)}}return i("POST",e,n)};exports.postXHRPromise=c;
},{"./config.js":"GITh"}],"IYwL":[function(require,module,exports) {
"use strict";require("./modules/main.js");var e=require("./modules/config.js"),t=require("./modules/spinner.js"),r=require("./modules/xhrPromise.js"),a=require("./modules/alert.js"),n=document.querySelector('[data-media-category="spare"] > div');n.classList.add("new-category");var o=0,i=function(){var e=n.cloneNode(!0),t=o+++"n";e.querySelectorAll("input[name^=category]").forEach(function(e){e.name=e.name.replace(/(.+?\[)(\].+)/,"$1"+t+"$2")}),document.querySelector('[data-category="wrapper"]').appendChild(e)},d=function(n){if(n.target.dataset.deleteCategoryPrompt&&confirm(n.target.dataset.deleteCategoryPrompt)){var o=parseInt(n.target.dataset.categoryId),i=n.target.closest('[data-category="parent"]');isNaN(o)?i.remove():((0,t.enableSpinner)(),(0,r.postXHRPromise)(e.pitonConfig.routes.adminMediaCategoryDelete,{categoryId:o}).then(function(){i.remove()}).then(function(){(0,t.disableSpinner)()}).catch(function(e){(0,t.disableSpinner)(),(0,a.alertInlineMessage)("danger","Failed to Delete Category",e)}))}};document.querySelector('[data-category="add"]').addEventListener("click",i,!1),document.addEventListener("click",d,!1);
},{"./modules/main.js":"mlrC","./modules/config.js":"GITh","./modules/spinner.js":"iiz7","./modules/xhrPromise.js":"ZwDW","./modules/alert.js":"Jvqk"}]},{},["IYwL"], null)