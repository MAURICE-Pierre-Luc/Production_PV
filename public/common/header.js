const header = document.getElementById("header");

header.innerHTML = `
    <div class="header-container">

        <a href="../installation/installation.html" class="logo">
            Production PV
        </a>

        <nav class="navigation">

            <a href="../installation/installation.html" class="nav-link" data-page="page1">
                Installation
            </a>

            <a href="../production/production.html" class="nav-link" data-page="page2">
                Production
            </a>

            <a href="../tarification/tarification.html" class="nav-link" data-page="page3">
                Tarifs
            </a>

            <a href="../login/login.html" class="nav-link" data-page="page4">
                Import
            </a>

        </nav>

    </div>
`;


/*
 * Détermine automatiquement la page actuelle
 * pour mettre le lien correspondant en évidence.
 */

const currentPage = window.location.pathname
    .split("/")
    .filter(Boolean)
    .at(-2);

const currentLink = document.querySelector(
    `.nav-link[data-page="${currentPage}"]`
);

if (currentLink) {
    currentLink.classList.add("active");
}