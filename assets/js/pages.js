// --------------------------------------------------------
// Page List JS
// --------------------------------------------------------

// Listen for page list status filter changes and reload
const pageListFilter = document.querySelector('.jsPageStatusFilter');
if (pageListFilter) {
    // If this page has a status filter, get the container div reference
    const pageList = document.querySelector('.list-items-wrapper');

    pageListFilter.addEventListener("change", (f) => {
        let filter  = pageListFilter.options[pageListFilter.selectedIndex].value;

        if (filter !== 'x') {
            // Remove existing page rows
            while (pageList.firstChild) {
                pageList.removeChild(pageList.lastChild);
            }

            // Get server data
            getXHRPromise(pitonConfig.routes.adminPageGet, {'pageStatus': filter})
                .then((data) => {
                    pageList.insertAdjacentHTML('afterbegin', data);
                }).catch(function (error) {
                    console.log('Something went wrong', error);
                });
        }
    }, false);
}
