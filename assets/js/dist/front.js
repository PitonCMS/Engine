(()=>{
/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright 2018 Wolfgang Moritz
 * @license   AGPL-3.0-or-later with Theme Exception. See LICENSE file for details.. See LICENSE file for details.
 */
/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright 2021 Wolfgang Moritz
 * @license   AGPL-3.0-or-later with Theme Exception. See LICENSE file for details.. See LICENSE file for details.
 */
let e={csrfTokenRequestHeader:"Piton-CSRF-Token",routes:{adminPageGet:"/admin/page/get",adminPageElementGet:"/admin/page/element/get",adminPageElementDelete:"/admin/page/element/delete",adminMessageSave:"/admin/message/save",adminMessageGet:"/admin/message/get",adminMessageCountGet:"/admin/message/getnewmessagecount",adminMedia:"/admin/media/",adminMediaSave:"/admin/media/save",adminMediaGet:"/admin/media/get/",adminMediaControlsGet:"/admin/media/getmediacontrols",adminMediaCategorySaveOrder:"/admin/media/category/saveorder",adminMediaDelete:"/admin/media/delete",adminMediaCategoryDelete:"/admin/media/category/delete",adminMediaUploadFormGet:"/admin/media/uploadform",adminMediaUploadFile:"/admin/media/upload",adminCollection:"/admin/collection/",adminNavigationDelete:"/admin/navigation/delete",submitMessage:"/submitmessage"}};"object"==typeof config&&(e={...e,...config})
/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright 2018 Wolfgang Moritz
 * @license   AGPL-3.0-or-later with Theme Exception. See LICENSE file for details.. See LICENSE file for details.
 */;const t=function(t,a,n){let r=new XMLHttpRequest;return new Promise(((i,d)=>{let o;r.onreadystatechange=()=>{if(r.readyState===XMLHttpRequest.DONE)try{if(200===r.status){if(o=JSON.parse(r.responseText),"success"===o.status)return i(o.text);throw new Error(`Application Error ${o.text}`)}throw new Error(`Server Error ${r.status} ${r.statusText}.`)}catch(e){if(!(e instanceof Error)){let e=new Error(e)}return d(e.message)}},r.open(t,a,!0),r.setRequestHeader("X-Requested-With","XMLHttpRequest"),"POST"===t&&e.csrfTokenValue&&r.setRequestHeader(e.csrfTokenRequestHeader,e.csrfTokenValue),r.send(n)}))},a=function(e,a){let n;if(a instanceof FormData)n=a;else{n=new FormData;for(let[e,t]of Object.entries(a))n.append(e,t)}return t("POST",e,n)},n="alt@example.com";document.querySelector('input[name="alt-email"]')?.setAttribute("value",n);const r=document.querySelector('[data-contact-response="true"]');document.addEventListener("submit",(function(t){if("true"!==t.target.dataset.contactForm)return;if(t.preventDefault(),t.target.querySelector(".alt-email")&&t.target.querySelector(".alt-email").value!==n)return;let i=t.target.dataset.contactFormButtonText?t.target.dataset.contactFormButtonText:"Sending...";t.target.querySelector('button[type="submit"]').innerHTML=i,a(e.routes.submitMessage,new FormData(t.target)).then((e=>{t.target.remove(),r&&(r.hidden=!1)})).catch((e=>{t.target.innerHTML=`<p>${e}</p>`}))}),!1)})();