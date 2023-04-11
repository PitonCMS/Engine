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
let n={csrfTokenRequestHeader:"Piton-CSRF-Token",routes:{adminPageGet:"/admin/page/get",adminPageElementGet:"/admin/page/element/get",adminPageElementDelete:"/admin/page/element/delete",adminMessageSave:"/admin/message/save",adminMessageGet:"/admin/message/get",adminMessageCountGet:"/admin/message/getnewmessagecount",adminMedia:"/admin/media/",adminMediaSave:"/admin/media/save",adminMediaGet:"/admin/media/get/",adminMediaControlsGet:"/admin/media/getmediacontrols",adminMediaCategorySaveOrder:"/admin/media/category/saveorder",adminMediaDelete:"/admin/media/delete",adminMediaCategoryDelete:"/admin/media/category/delete",adminMediaUploadFormGet:"/admin/media/uploadform",adminMediaUploadFile:"/admin/media/upload",adminCollection:"/admin/collection/",adminNavigationDelete:"/admin/navigation/delete",submitMessage:"/submitmessage"}};"object"==typeof config&&(n={...n,...config});document.querySelector('[data-alert-modal="true"]');document.addEventListener("click",(function(e){"alert"===e.target.dataset.dismiss&&e.target.closest('[data-alert="container"]')?.remove()}),!1)})();