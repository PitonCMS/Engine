parcelRequire=function(e,r,t,n){var i,o="function"==typeof parcelRequire&&parcelRequire,u="function"==typeof require&&require;function f(t,n){if(!r[t]){if(!e[t]){var i="function"==typeof parcelRequire&&parcelRequire;if(!n&&i)return i(t,!0);if(o)return o(t,!0);if(u&&"string"==typeof t)return u(t);var c=new Error("Cannot find module '"+t+"'");throw c.code="MODULE_NOT_FOUND",c}p.resolve=function(r){return e[t][1][r]||r},p.cache={};var l=r[t]=new f.Module(t);e[t][0].call(l.exports,p,l,l.exports,this)}return r[t].exports;function p(e){return f(p.resolve(e))}}f.isParcelRequire=!0,f.Module=function(e){this.id=e,this.bundle=f,this.exports={}},f.modules=e,f.cache=r,f.parent=o,f.register=function(r,t){e[r]=[function(e,r){r.exports=t},{}]};for(var c=0;c<t.length;c++)try{f(t[c])}catch(e){i||(i=e)}if(t.length){var l=f(t[t.length-1]);"object"==typeof exports&&"undefined"!=typeof module?module.exports=l:"function"==typeof define&&define.amd?define(function(){return l}):n&&(this[n]=l)}if(parcelRequire=f,i)throw i;return f}({"ncrz":[function(require,module,exports) {
"use strict";Object.defineProperty(exports,"__esModule",{value:!0}),exports.disableFormControl=exports.enableFormControl=void 0;var e=function(e){e&&e.disabled&&(e.disabled=!1)};exports.enableFormControl=e;var o=function(e){e&&!e.disabled&&(e.disabled=!0)};exports.disableFormControl=o;
},{}],"Jvqk":[function(require,module,exports) {
"use strict";function e(t){return(e="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e})(t)}Object.defineProperty(exports,"__esModule",{value:!0}),exports.dismissAlertInlineMessage=exports.alertInlineMessage=void 0;var t=function(e){"alert"===e.target.dataset.dismiss&&e.target.closest('[data-alert="container"]').remove()};exports.dismissAlertInlineMessage=t;var n=function(t,n,r){var o=document.createElement("div");o.innerHTML=pitonConfig.alertInlineHTML,o.querySelector('[data-alert="container"]').classList.add("alert-"+t),o.querySelector('[data-alert="heading"]').innerHTML=n,"object"===e(r)&&(r=r.join("<br>")),o.querySelector('[data-alert="content"]').innerHTML=r,document.querySelector("main.main-content").insertAdjacentHTML("afterbegin",o.innerHTML),window.scrollTo(0,0)};exports.alertInlineMessage=n;
},{}],"BLPW":[function(require,module,exports) {
"use strict";Object.defineProperty(exports,"__esModule",{value:!0}),exports.collapseToggle=void 0;var e=function(e){if("toggle"===e.target.dataset.collapse){var t=e.target.closest('[data-collapse="parent"]').querySelector('[data-collapse="target"]');t&&t.classList.toggle("collapsed")}};exports.collapseToggle=e;
},{}],"mlrC":[function(require,module,exports) {
"use strict";var e=require("./formControl.js"),t=require("./alert.js"),n=require("./collapse.js");document.querySelectorAll("form").forEach(function(t){var n=t.querySelectorAll('[data-form-button="save"]');n&&(n.forEach(function(t){(0,e.disableFormControl)(t)}),t.addEventListener("input",function(t){n.forEach(function(t){(0,e.enableFormControl)(t)})},!1)),t.querySelectorAll('[data-form-button="cancel"]').forEach(function(e){e.addEventListener("click",function(e){confirm("Click Ok to discard your changes, or cancel continue editing?")||e.preventDefault()},!1)}),t.querySelectorAll("[data-delete-prompt]").forEach(function(e){e.addEventListener("click",function(e){confirm(e.target.dataset.deletePrompt)||e.preventDefault()},!1)})}),document.addEventListener("click",t.dismissAlertInlineMessage,!1),document.addEventListener("click",n.collapseToggle,!1);
},{"./formControl.js":"ncrz","./alert.js":"Jvqk","./collapse.js":"BLPW"}],"wuKc":[function(require,module,exports) {
"use strict";require("./modules/main.js");var e=[],t=document.querySelectorAll('[data-add-nav="page"] input'),a=document.querySelectorAll('[data-add-nav="collection"] input'),n=document.querySelectorAll('[data-add-nav="placeholder"] input'),l=document.querySelector('[data-navigation-element="1"] > div'),c=document.querySelector('[data-navigation="container"]'),o=0,r=function(){e.forEach(function(e){var t=l.cloneNode(!0),a=o+++"n";t.querySelectorAll("input[name^=nav]").forEach(function(e){e.name=e.name.replace(/(.+?\[)(\].+)/,"$1"+a+"$2")}),e.pageId?(t.querySelector('input[name$="[pageId]"]').value=e.pageId,t.querySelector('[data-nav="title"]').innerHTML=e.pageTitle,t.querySelector('[data-nav="type"]').innerHTML="page",t.querySelector('[data-nav="pageTitle"]').innerHTML=e.pageTitle,t.querySelector('[data-nav="pageTitle"]').parentElement.classList.remove("d-none")):e.navTitle?(t.querySelector('[data-nav="title"]').innerHTML=e.navTitle,t.querySelector('[data-nav="type"]').innerHTML="placeholder",t.querySelector('input[name$="[navTitle]"]').value=e.navTitle,t.querySelector('input[name$="[url]"]').value=e.url,t.querySelector('input[name$="[url]"]').parentElement.classList.remove("d-none")):e.collectionId&&(t.querySelector('input[name$="[collectionId]"]').value=e.collectionId,t.querySelector('[data-nav="title"]').innerHTML=e.collectionTitle,t.querySelector('[data-nav="type"]').innerHTML="collection",t.querySelector('[data-nav="collectionTitle"]').innerHTML=e.collectionTitle,t.querySelector('[data-nav="collectionTitle"]').parentElement.classList.remove("d-none")),c.appendChild(t)}),e.length=0},i=function(){t.forEach(function(t){if(t.checked){var a={pageId:t.dataset.pageId,pageTitle:t.dataset.pageTitle};t.checked=!1,e.push(a)}}),r()},d=function(){a.forEach(function(t){if(t.checked){var a={collectionId:t.dataset.collectionId,collectionTitle:t.dataset.collectionTitle};t.checked=!1,e.push(a)}}),r()},u=function(){if(n[0].value){var t={navTitle:n[0].value,url:n[1].value};n[0].value="",n[1].value="",e.push(t)}r()};document.querySelector('[data-add-nav="pageButton"]').addEventListener("click",i,!1),document.querySelector('[data-add-nav="collectionButton"]').addEventListener("click",d,!1),document.querySelector('[data-add-nav="placeholderButton"]').addEventListener("click",u,!1);
},{"./modules/main.js":"mlrC"}]},{},["wuKc"], null)
//# sourceMappingURL=/navigation.js.map