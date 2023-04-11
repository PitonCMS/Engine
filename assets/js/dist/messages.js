(()=>{document.addEventListener("input",(function(e){if(!e.target.closest("form"))return;let t=e.target.closest("form").querySelectorAll('[data-form-button="save"], [data-form-button="cancel"]');t&&t.forEach((e=>{e.disabled=!1}))}),!1),document.addEventListener("click",(function(e){e.target.dataset.deletePrompt&&(confirm(e.target.dataset.deletePrompt)||e.preventDefault())}),!1),document.addEventListener("click",(function(e){if("cancel"!==e.target.dataset.formButton)return;if(e.stopPropagation(),!confirm("Click Ok to discard your changes, or Cancel continue editing."))return void e.preventDefault();let t=e.target.closest("form").querySelectorAll('[data-form-button="save"], [data-form-button="cancel"]');t&&(e.target.dataset.formResetHref?(e.preventDefault(),window.location=e.target.dataset.formResetHref):setTimeout((()=>{t.forEach((e=>{e.disabled=!0}))}),0))}),!1);
/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright 2018 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */
const e="collapsed",t=Array.from(document.querySelectorAll('[data-collapse-toggle^="newElementButton"]')),a=function(e){let a=t.findIndex((t=>t.dataset.collapseToggle===e));if(-1!==a){t.slice(a+1).forEach((e=>{e.classList.toggle("d-none")}))}};document.addEventListener("click",(function(t){if(!t.target.closest("[data-collapse-toggle]"))return;let n=t.target.closest("[data-collapse-toggle]").dataset.collapseToggle;n.match(/^newElementButton/)&&a(n),document.querySelector(`[data-collapse-target="${n}"]`).classList.toggle(e)}),!1),document.addEventListener("click",(function(t){if(!t.target.closest("[data-collapse-auto]"))return;let n=t.target.closest("[data-collapse-auto]").dataset.collapseAuto;document.querySelector(`[data-collapse-target="${n}"]`).classList.add(e),n.match(/^newElementButton/)&&a(n)}),!1);
/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright 2018 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */
/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright 2021 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */
let n={csrfTokenRequestHeader:"Piton-CSRF-Token",routes:{adminPageGet:"/admin/page/get",adminPageElementGet:"/admin/page/element/get",adminPageElementDelete:"/admin/page/element/delete",adminMessageSave:"/admin/message/save",adminMessageGet:"/admin/message/get",adminMessageCountGet:"/admin/message/getnewmessagecount",adminMedia:"/admin/media/",adminMediaSave:"/admin/media/save",adminMediaGet:"/admin/media/get/",adminMediaControlsGet:"/admin/media/getmediacontrols",adminMediaCategorySaveOrder:"/admin/media/category/saveorder",adminMediaDelete:"/admin/media/delete",adminMediaCategoryDelete:"/admin/media/category/delete",adminMediaUploadFormGet:"/admin/media/uploadform",adminMediaUploadFile:"/admin/media/upload",adminCollection:"/admin/collection/",adminNavigationDelete:"/admin/navigation/delete",submitMessage:"/submitmessage"}};"object"==typeof config&&(n={...n,...config});const r=document.querySelector('[data-alert-modal="true"]'),o=function(e,t,a){if(a=Array.isArray(a)&&null!==a?a.join("<br>"):a instanceof Error?a.message:"object"==typeof a&&null!==a?Object.values(a).join("<br>"):String(a),r){let o=document.createElement("div");o.innerHTML=n.alertInlineHTML,o.querySelector('[data-alert="container"]').classList.add("alert-"+e),o.querySelector('[data-alert="heading"]').innerHTML=t,o.querySelector('[data-alert="content"]').innerHTML=a,r.insertAdjacentHTML("afterbegin",o.innerHTML),window.scrollTo(0,0)}else alert(a)};document.addEventListener("click",(function(e){"alert"===e.target.dataset.dismiss&&e.target.closest('[data-alert="container"]')?.remove()}),!1);
/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright 2018 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */
/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright 2018 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */
/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright 2018 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */
const s=function(){document.body.insertAdjacentHTML("afterbegin",n.spinnerHTML)},i=function(){document.querySelector('[data-spinner="true"]')?.remove()},d=function(e,t,a){let r=new XMLHttpRequest;return new Promise(((o,s)=>{let i;r.onreadystatechange=()=>{if(r.readyState===XMLHttpRequest.DONE)try{if(200===r.status){if(i=JSON.parse(r.responseText),"success"===i.status)return o(i.text);throw new Error(`Application Error ${i.text}`)}throw new Error(`Server Error ${r.status} ${r.statusText}.`)}catch(e){if(!(e instanceof Error)){let e=new Error(e)}return s(e.message)}},r.open(e,t,!0),r.setRequestHeader("X-Requested-With","XMLHttpRequest"),"POST"===e&&n.csrfTokenValue&&r.setRequestHeader(n.csrfTokenRequestHeader,n.csrfTokenValue),r.send(a)}))},c=function(e,t){if(t){let a;if(t instanceof URLSearchParams)a=t;else{a=new URLSearchParams;for(let[e,n]of Object.entries(t))a.append(e,n)}e+="?"+a.toString()}return d("GET",e)},l=function(e,t){let a;if(t instanceof FormData)a=t;else{a=new FormData;for(let[e,n]of Object.entries(t))a.append(e,n)}return d("POST",e,a)};let u;const m=function(e='[data-query="content"]'){return document.querySelector(e)},g=function(e){return u||console.error("Module xhrQuery requestPath is not set."),s(),c(u,e).then((e=>(function(){if(m())for(;m().firstChild;)m().removeChild(m().lastChild)}(),e))).then((e=>{m().insertAdjacentHTML("afterbegin",e)})).then((()=>{i()})).catch((e=>{i(),o("danger","Failed to Get Results",e)}))};document.addEventListener("click",(function(e){if(e.target.closest(".pagination > div")){e.preventDefault();let t=e.target.closest(".pagination > div").querySelector("a").href,a=new URL(t),n=new URLSearchParams(a.search);return g(n)}}),!1);const f=function(){let e=document.querySelectorAll('[data-filter="options"] input'),t={};return e.forEach((e=>{e.checked&&(t.hasOwnProperty(e.name)?t[e.name]+=","+e.value:t[e.name]=e.value)})),g(t)},p=function(){let e={terms:document.querySelector('[data-filter="search"] input').value};return document.querySelectorAll('[data-filter="options"] input').forEach((e=>{e.checked&&(e.checked=!1)})),g(e)};var h;document.addEventListener("click",(function(e){"apply"===e.target.dataset.filterControl&&f()}),!1),document.addEventListener("click",(function(e){if("clear"===e.target.dataset.filterControl){e.target.closest('[data-filter="options"]').querySelectorAll("input").forEach((e=>{e.checked=!1}))}}),!1),document.addEventListener("click",(e=>{e.target.closest('[data-filter-control="search"]')&&p()}),!1),document.addEventListener("keypress",(e=>{e.target.closest('[data-filter="search"]')&&"Enter"===e.key&&p()}),!1),h=n.routes.adminMessageGet,u=h;const v=document.querySelector('[data-message="count"]');document.addEventListener("click",(function(e){if(!e.target.dataset.messageControl)return;let t={messageId:e.target.closest('[data-message="parent"]').dataset.messageId};if("delete"===e.target.dataset.messageControl){if(!confirm(e.target.dataset.messageDeletePrompt))return;t.control="delete"}else"archive"===e.target.dataset.messageControl?t.control="archive":"read"===e.target.dataset.messageControl&&(t.control="read");s(),l(n.routes.adminMessageSave,t).then((()=>{c(n.routes.adminMessageCountGet).then((e=>{v.innerHTML=e})).catch((e=>{o("danger","Unable to Update Inbox Message Count",e)}))})).then((()=>{f()})).then((()=>{i()})).catch((e=>{i(),o("danger","Failed to Update Message",e)}))}),!1)})();