parcelRequire=function(e,r,t,n){var i,o="function"==typeof parcelRequire&&parcelRequire,u="function"==typeof require&&require;function f(t,n){if(!r[t]){if(!e[t]){var i="function"==typeof parcelRequire&&parcelRequire;if(!n&&i)return i(t,!0);if(o)return o(t,!0);if(u&&"string"==typeof t)return u(t);var c=new Error("Cannot find module '"+t+"'");throw c.code="MODULE_NOT_FOUND",c}p.resolve=function(r){return e[t][1][r]||r},p.cache={};var l=r[t]=new f.Module(t);e[t][0].call(l.exports,p,l,l.exports,this)}return r[t].exports;function p(e){return f(p.resolve(e))}}f.isParcelRequire=!0,f.Module=function(e){this.id=e,this.bundle=f,this.exports={}},f.modules=e,f.cache=r,f.parent=o,f.register=function(r,t){e[r]=[function(e,r){r.exports=t},{}]};for(var c=0;c<t.length;c++)try{f(t[c])}catch(e){i||(i=e)}if(t.length){var l=f(t[t.length-1]);"object"==typeof exports&&"undefined"!=typeof module?module.exports=l:"function"==typeof define&&define.amd?define(function(){return l}):n&&(this[n]=l)}if(parcelRequire=f,i)throw i;return f}({"ncrz":[function(require,module,exports) {
"use strict";Object.defineProperty(exports,"__esModule",{value:!0}),exports.disableFormControl=exports.enableFormControl=void 0;var e=function(e){e&&e.disabled&&(e.disabled=!1)};exports.enableFormControl=e;var o=function(e){e&&!e.disabled&&(e.disabled=!0)};exports.disableFormControl=o;
},{}],"Jvqk":[function(require,module,exports) {
"use strict";function e(t){return(e="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e})(t)}Object.defineProperty(exports,"__esModule",{value:!0}),exports.dismissAlertInlineMessage=exports.alertInlineMessage=void 0;var t=function(e){"alert"===e.target.dataset.dismiss&&e.target.closest('[data-alert="container"]').remove()};exports.dismissAlertInlineMessage=t;var n=function(t,n,r){var o=document.createElement("div");o.innerHTML=pitonConfig.alertInlineHTML,o.querySelector('[data-alert="container"]').classList.add("alert-"+t),o.querySelector('[data-alert="heading"]').innerHTML=n,"object"===e(r)&&(r=r.join("<br>")),o.querySelector('[data-alert="content"]').innerHTML=r,document.querySelector("main.main-content").insertAdjacentHTML("afterbegin",o.innerHTML),window.scrollTo(0,0)};exports.alertInlineMessage=n;
},{}],"BLPW":[function(require,module,exports) {
"use strict";Object.defineProperty(exports,"__esModule",{value:!0}),exports.collapseToggle=void 0;var e=function(e){if("toggle"===e.target.dataset.collapse){var t=e.target.closest('[data-collapse="parent"]').querySelector('[data-collapse="target"]');t&&t.classList.toggle("collapsed")}};exports.collapseToggle=e;
},{}],"mlrC":[function(require,module,exports) {
"use strict";var e=require("./formControl.js"),t=require("./alert.js"),n=require("./collapse.js");document.querySelectorAll("form").forEach(function(t){var n=t.querySelectorAll('[data-form-button="save"]');n&&(n.forEach(function(t){(0,e.disableFormControl)(t)}),t.addEventListener("input",function(t){n.forEach(function(t){(0,e.enableFormControl)(t)})},!1)),t.querySelectorAll('[data-form-button="cancel"]').forEach(function(e){e.addEventListener("click",function(e){confirm("Click Ok to discard your changes, or cancel continue editing?")||e.preventDefault()},!1)}),t.querySelectorAll("[data-delete-prompt]").forEach(function(e){e.addEventListener("click",function(e){confirm(e.target.dataset.deletePrompt)||e.preventDefault()},!1)})}),document.addEventListener("click",t.dismissAlertInlineMessage,!1),document.addEventListener("click",n.collapseToggle,!1);
},{"./formControl.js":"ncrz","./alert.js":"Jvqk","./collapse.js":"BLPW"}],"uxT7":[function(require,module,exports) {
"use strict";var e;Object.defineProperty(exports,"__esModule",{value:!0}),exports.getMovedElement=exports.dragEndHandler=exports.dragDropHandler=exports.dragLeaveHandler=exports.dragOverHandler=exports.dragEnterHandler=exports.dragStartHandler=void 0;var t=new Event("input",{bubbles:!0}),r="border: 1px dashed #000; height: 20px;",a="border: 1px dashed #000; height: 60px;",n=document.createElement("div");n.classList.add("drag-drop"),n.style.cssText=r;var o=function(){return e};exports.getMovedElement=o;var d=function(t){e=t.target,t.dataTransfer.setData("text/plain",null),t.dataTransfer.dropEffect="move",setTimeout(function(){document.querySelectorAll('[draggable="true"]').forEach(function(e){e.parentElement.insertBefore(n.cloneNode(),e),e.parentElement.lastElementChild===e&&e.parentElement.appendChild(n.cloneNode())}),document.querySelectorAll('[data-drop-zone="1"]').forEach(function(e){e.parentElement.insertBefore(n.cloneNode(),e)})},0)};exports.dragStartHandler=d;var s=function(e){e.preventDefault(),e.stopPropagation(),e.dataTransfer.dropEffect="move",e.target.matches(".drag-drop")&&(e.target.classList.add("drag-hover"),e.target.style.cssText=a)};exports.dragEnterHandler=s;var l=function(e){e.preventDefault(),e.stopPropagation(),e.dataTransfer.dropEffect="move"};exports.dragOverHandler=l;var p=function(e){e.preventDefault(),e.stopPropagation(),e.dataTransfer.dropEffect="move",e.target.matches(".drag-drop")&&(e.target.classList.remove("drag-hover"),e.target.style.cssText=r)};exports.dragLeaveHandler=p;var c=function(t){t.preventDefault(),t.stopPropagation(),e!==t.target&&t.target.matches(".drag-drop")&&t.target.parentElement.insertBefore(e,t.target.nextSibling)};exports.dragDropHandler=c;var g=function(r){document.querySelectorAll(".drag-drop").forEach(function(e){e.remove()}),e.dispatchEvent(t)};exports.dragEndHandler=g;
},{}],"wuKc":[function(require,module,exports) {
"use strict";require("./modules/main.js");var e=require("./modules/drag.js"),t=[],a=document.querySelectorAll('[data-add-nav="page"] input'),n=document.querySelectorAll('[data-add-nav="collection"] input'),r=document.querySelectorAll('[data-add-nav="placeholder"] input'),l=document.querySelector('[data-navigation="spare"] > div'),d=document.querySelector('[data-navigation-container="1"]'),o=0,c=function(){t.forEach(function(e){var t=l.cloneNode(!0),a=o+++"n";t.querySelectorAll("input[name^=nav]").forEach(function(e){e.name=e.name.replace(/(.+?\[)(\].+)/,"$1"+a+"$2")}),t.dataset.navId=a,e.pageId?(t.querySelector('input[name$="[pageId]"]').value=e.pageId,t.querySelector('[data-nav="title"]').innerHTML=e.pageTitle,t.querySelector('[data-nav="type"]').innerHTML="page",t.querySelector('[data-nav="pageTitle"]').innerHTML=e.pageTitle,t.querySelector('[data-nav="pageTitle"]').parentElement.classList.remove("d-none")):e.navTitle?(t.querySelector('[data-nav="title"]').innerHTML=e.navTitle,t.querySelector('[data-nav="type"]').innerHTML="placeholder",t.querySelector('input[name$="[navTitle]"]').value=e.navTitle,t.querySelector('input[name$="[url]"]').value=e.url,t.querySelector('input[name$="[url]"]').parentElement.classList.remove("d-none")):e.collectionId&&(t.querySelector('input[name$="[collectionId]"]').value=e.collectionId,t.querySelector('[data-nav="title"]').innerHTML=e.collectionTitle,t.querySelector('[data-nav="type"]').innerHTML="collection",t.querySelector('[data-nav="collectionTitle"]').innerHTML=e.collectionTitle,t.querySelector('[data-nav="collectionTitle"]').parentElement.classList.remove("d-none")),d.appendChild(t),t.dispatchEvent(new Event("input",{bubbles:!0}))}),t.length=0},i=function(){a.forEach(function(e){if(e.checked){var a={pageId:e.dataset.pageId,pageTitle:e.dataset.pageTitle};e.checked=!1,t.push(a)}}),c()},u=function(){n.forEach(function(e){if(e.checked){var a={collectionId:e.dataset.collectionId,collectionTitle:e.dataset.collectionTitle};e.checked=!1,t.push(a)}}),c()},v=function(){if(r[0].value){var e={navTitle:r[0].value,url:r[1].value};r[0].value="",r[1].value="",t.push(e)}c()},p=function(t){t.preventDefault(),t.stopPropagation();var a=(0,e.getMovedElement)();if(a!==t.target&&t.target.matches(".drag-drop")){var n=a.querySelector('input[name$="[parentId]"]').value,r=t.target.parentElement.closest('[data-navigation="parent"]');n===r.dataset.navId&&t.target.parentElement.insertBefore(a,t.target.nextSibling),n!==r.dataset.navId&&(a.querySelector('input[name$="[parentId]"]').value=r.dataset.navId,""===r.dataset.navId?a.classList.remove("sub-toggle-block"):a.classList.add("sub-toggle-block"),t.target.parentElement.insertBefore(a,t.target.nextSibling))}};document.querySelector('[data-add-nav="pageButton"]').addEventListener("click",i,!1),document.querySelector('[data-add-nav="collectionButton"]').addEventListener("click",u,!1),document.querySelector('[data-add-nav="placeholderButton"]').addEventListener("click",v,!1),document.querySelectorAll('[data-draggable="children"]').forEach(function(t){t.addEventListener("dragstart",e.dragStartHandler,!1),t.addEventListener("dragenter",e.dragEnterHandler,!1),t.addEventListener("dragover",e.dragOverHandler,!1),t.addEventListener("dragleave",e.dragLeaveHandler,!1),t.addEventListener("drop",p,!1),t.addEventListener("dragend",e.dragEndHandler,!1)}),document.querySelectorAll('[data-add-nav="page"], [data-add-nav="collection"], [data-add-nav="placeholder"]').forEach(function(e){e.addEventListener("input",function(e){e.stopPropagation()})});
},{"./modules/main.js":"mlrC","./modules/drag.js":"uxT7"}]},{},["wuKc"], null)
//# sourceMappingURL=/navigation.js.map