// --------------------------------------------------------
// Navigation
// --------------------------------------------------------
import './modules/main.js';

const navItems = [];
const navPages = document.querySelectorAll(`[data-add-nav="page"] input`);
const navPlaceholder = document.querySelectorAll(`[data-add-nav="placeholder"] input`);
const navElement = document.querySelector(`[data-navigation-element="1"] > div`);
const navContainer = document.querySelector(`[data-navigation="container"]`);
let navItemKey = 0;

/**
 * Append Navigation Elements
 */
const appendNavElements = function() {
  navItems.forEach(nav => {
    // Clone spare navigation element, and set unique name array key so POST array keeps inputs together
    let newNav = navElement.cloneNode(true);
    let arrayKey = (navItemKey++) + "n";
    newNav.querySelectorAll(`input[name^=nav]`).forEach(input => {
      input.name = input.name.replace(/(.+?\[)(\].+)/, "$1" + arrayKey + "$2");
    });

    // Set data
    if (nav.pageId) {
      newNav.querySelector(`input[name$="\[pageId\]"]`).value = nav.pageId;
      newNav.querySelector(`[data-nav="title"]`).innerHTML = nav.pageTitle;
      newNav.querySelector(`[data-nav="type"]`).innerHTML = "page";
      newNav.querySelector(`[data-nav="pageTitle"]`).innerHTML = nav.pageTitle;
      newNav.querySelector(`[data-nav="pageTitle"]`).parentElement.classList.remove("d-none");

    } else if (nav.navTitle) {
      newNav.querySelector(`[data-nav="title"]`).innerHTML = nav.navTitle;
      newNav.querySelector(`[data-nav="type"]`).innerHTML = "placeholder";
      newNav.querySelector(`input[name$="\[navTitle\]"]`).value = nav.navTitle;
      newNav.querySelector(`input[name$="\[url\]"]`).value = nav.url;
      newNav.querySelector(`input[name$="\[url\]"]`).parentElement.classList.remove("d-none");

    }

    navContainer.appendChild(newNav);
  });

  // Reset
  navItems.length = 0;
}

/**
 * Add Page Navigation
 */
const addPageNav = function() {
  navPages.forEach(element => {
    if (element.checked) {
      let navItem = {
        "pageId": element.dataset.pageId,
        "pageTitle": element.dataset.pageTitle
      }

      element.checked = false;
      navItems.push(navItem);
    }

  });

  appendNavElements();
}

/**
 * Add Placeholder Navigation
 */
const addPlaceholderNav = function() {
  if (navPlaceholder[0].value) {
    let navItem = {
      "navTitle": navPlaceholder[0].value,
      "url": navPlaceholder[1].value
    }

    navPlaceholder[0].value = "";
    navPlaceholder[1].value = "";
    navItems.push(navItem);
  }

  appendNavElements();
}

// Bind events
document.querySelector(`[data-add-nav="pageButton"]`).addEventListener("click", addPageNav, false);
document.querySelector(`[data-add-nav="placeholderButton"]`).addEventListener("click", addPlaceholderNav, false);
