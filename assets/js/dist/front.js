parcelRequire=function(e,r,t,n){var i,o="function"==typeof parcelRequire&&parcelRequire,u="function"==typeof require&&require;function f(t,n){if(!r[t]){if(!e[t]){var i="function"==typeof parcelRequire&&parcelRequire;if(!n&&i)return i(t,!0);if(o)return o(t,!0);if(u&&"string"==typeof t)return u(t);var c=new Error("Cannot find module '"+t+"'");throw c.code="MODULE_NOT_FOUND",c}p.resolve=function(r){return e[t][1][r]||r},p.cache={};var l=r[t]=new f.Module(t);e[t][0].call(l.exports,p,l,l.exports,this)}return r[t].exports;function p(e){return f(p.resolve(e))}}f.isParcelRequire=!0,f.Module=function(e){this.id=e,this.bundle=f,this.exports={}},f.modules=e,f.cache=r,f.parent=o,f.register=function(r,t){e[r]=[function(e,r){r.exports=t},{}]};for(var c=0;c<t.length;c++)try{f(t[c])}catch(e){i||(i=e)}if(t.length){var l=f(t[t.length-1]);"object"==typeof exports&&"undefined"!=typeof module?module.exports=l:"function"==typeof define&&define.amd?define(function(){return l}):n&&(this[n]=l)}if(parcelRequire=f,i)throw i;return f}({"GITh":[function(require,module,exports) {
"use strict";function e(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var a=Object.getOwnPropertySymbols(e);t&&(a=a.filter(function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable})),n.push.apply(n,a)}return n}function t(t){for(var a=1;a<arguments.length;a++){var i=null!=arguments[a]?arguments[a]:{};a%2?e(Object(i),!0).forEach(function(e){n(t,e,i[e])}):Object.getOwnPropertyDescriptors?Object.defineProperties(t,Object.getOwnPropertyDescriptors(i)):e(Object(i)).forEach(function(e){Object.defineProperty(t,e,Object.getOwnPropertyDescriptor(i,e))})}return t}function n(e,t,n){return t in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n,e}function a(e){return(a="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e})(e)}Object.defineProperty(exports,"__esModule",{value:!0}),exports.pitonConfig=void 0;var i={csrfTokenRequestHeader:"Piton-CSRF-Token",routes:{adminPageGet:"/admin/page/get",adminPageElementGet:"/admin/page/element/get",adminPageElementDelete:"/admin/page/element/delete",adminMessageSave:"/admin/message/save",adminMessageGet:"/admin/message/get",adminMessageCountGet:"/admin/message/getnewmessagecount",adminMedia:"/admin/media/",adminMediaSave:"/admin/media/save",adminMediaGet:"/admin/media/get/",adminMediaControlsGet:"/admin/media/getmediacontrols",adminMediaCategorySaveOrder:"/admin/media/category/saveorder",adminMediaDelete:"/admin/media/delete",adminMediaCategoryDelete:"/admin/media/category/delete",adminMediaUploadFormGet:"/admin/media/uploadform",adminMediaUploadFile:"/admin/media/upload",adminCollection:"/admin/collection/",adminNavigationDelete:"/admin/navigation/delete",submitMessage:"/submitmessage"}};exports.pitonConfig=i,"object"===("undefined"==typeof config?"undefined":a(config))&&(exports.pitonConfig=i=t(t({},i),config));
},{}],"ZwDW":[function(require,module,exports) {
"use strict";Object.defineProperty(exports,"__esModule",{value:!0}),exports.postXHRPromise=exports.getXHRPromise=void 0;var e=require("./config.js");function r(e,r){return s(e)||a(e,r)||n(e,r)||t()}function t(){throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}function n(e,r){if(e){if("string"==typeof e)return o(e,r);var t=Object.prototype.toString.call(e).slice(8,-1);return"Object"===t&&e.constructor&&(t=e.constructor.name),"Map"===t||"Set"===t?Array.from(e):"Arguments"===t||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t)?o(e,r):void 0}}function o(e,r){(null==r||r>e.length)&&(r=e.length);for(var t=0,n=new Array(r);t<r;t++)n[t]=e[t];return n}function a(e,r){var t=null==e?null:"undefined"!=typeof Symbol&&e[Symbol.iterator]||e["@@iterator"];if(null!=t){var n,o,a=[],s=!0,i=!1;try{for(t=t.call(e);!(s=(n=t.next()).done)&&(a.push(n.value),!r||a.length!==r);s=!0);}catch(u){i=!0,o=u}finally{try{s||null==t.return||t.return()}finally{if(i)throw o}}return a}}function s(e){if(Array.isArray(e))return e}var i=function(r,t,n){var o=new XMLHttpRequest;return new Promise(function(a,s){var i;o.onreadystatechange=function(){if(o.readyState===XMLHttpRequest.DONE)try{if(200===o.status){if("success"===(i=JSON.parse(o.responseText)).status)return a(i.text);throw new Error("Application Error ".concat(i.text))}throw new Error("Server Error ".concat(o.status," ").concat(o.statusText,"."))}catch(r){if(!(r instanceof Error))var e=new Error(e);return s(r.message)}},o.open(r,t,!0),o.setRequestHeader("X-Requested-With","XMLHttpRequest"),"POST"===r&&e.pitonConfig.csrfTokenValue&&o.setRequestHeader(e.pitonConfig.csrfTokenRequestHeader,e.pitonConfig.csrfTokenValue),o.send(n)})},u=function(e,t){if(t){var n;if(t instanceof URLSearchParams)n=t;else{n=new URLSearchParams;for(var o=0,a=Object.entries(t);o<a.length;o++){var s=r(a[o],2),u=s[0],c=s[1];n.append(u,c)}}e+="?"+n.toString()}return i("GET",e)};exports.getXHRPromise=u;var c=function(e,t){var n;if(t instanceof FormData)n=t;else{n=new FormData;for(var o=0,a=Object.entries(t);o<a.length;o++){var s=r(a[o],2),u=s[0],c=s[1];n.append(u,c)}}return i("POST",e,n)};exports.postXHRPromise=c;
},{"./config.js":"GITh"}],"JCFq":[function(require,module,exports) {
"use strict";var t=require("./modules/config.js"),e=require("./modules/xhrPromise.js"),a="alt@example.com";document.querySelectorAll('input[name="alt-email"]').forEach(function(t){t.setAttribute("value",a)});var r=function(r){if("true"===r.target.dataset.contactForm&&(r.preventDefault(),!r.target.querySelector(".alt-email")||r.target.querySelector(".alt-email").value===a)){var n=r.target.dataset.contactFormButtonText?r.target.dataset.contactFormButtonText:"Sending...";r.target.querySelector('button[type="submit"]').innerHTML=n,(0,e.postXHRPromise)(t.pitonConfig.routes.submitMessage,new FormData(r.target)).then(function(t){r.target.innerHTML="<p>".concat(t,"</p>")}).catch(function(t){r.target.innerHTML="<p>".concat(t,"</p>")})}};document.addEventListener("submit",r,!1);
},{"./modules/config.js":"GITh","./modules/xhrPromise.js":"ZwDW"}]},{},["JCFq"], null)