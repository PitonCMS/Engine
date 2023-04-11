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
const d=function(){document.querySelector('[data-spinner="true"]')?.remove()},i=function(e,t,a){let r=new XMLHttpRequest;return new Promise(((o,d)=>{let i;r.onreadystatechange=()=>{if(r.readyState===XMLHttpRequest.DONE)try{if(200===r.status){if(i=JSON.parse(r.responseText),"success"===i.status)return o(i.text);throw new Error(`Application Error ${i.text}`)}throw new Error(`Server Error ${r.status} ${r.statusText}.`)}catch(e){if(!(e instanceof Error)){let e=new Error(e)}return d(e.message)}},r.open(e,t,!0),r.setRequestHeader("X-Requested-With","XMLHttpRequest"),"POST"===e&&n.csrfTokenValue&&r.setRequestHeader(n.csrfTokenRequestHeader,n.csrfTokenValue),r.send(a)}))},l=function(e,t){let a;if(t instanceof FormData)a=t;else{a=new FormData;for(let[e,n]of Object.entries(t))a.append(e,n)}return i("POST",e,a)},s=document.querySelector('[data-media-category="spare"] > div');s.classList.add("new-category");let c=0;document.querySelector('[data-category="add"]').addEventListener("click",(function(){let e=s.cloneNode(!0),t=c+++"n";e.querySelectorAll("input[name^=category]").forEach((e=>{e.name=e.name.replace(/(.+?\[)(\].+)/,"$1"+t+"$2")})),document.querySelector('[data-category="wrapper"]').appendChild(e)}),!1),document.addEventListener("click",(function(e){if(!e.target.dataset.deleteCategoryPrompt)return;if(!confirm(e.target.dataset.deleteCategoryPrompt))return;let t=parseInt(e.target.dataset.categoryId),a=e.target.closest('[data-category="parent"]');isNaN(t)?a.remove():(document.body.insertAdjacentHTML("afterbegin",n.spinnerHTML),l(n.routes.adminMediaCategoryDelete,{categoryId:t}).then((()=>{a.remove()})).then((()=>{d()})).catch((e=>{d(),o("danger","Failed to Delete Category",e)})))}),!1)})();