/*
 * Données temporaires.
 *
 * Elles seront remplacées par les données
 * récupérées depuis l'API Symfony.
 */

const tariffData = {

    blue: {
        hc: "---",
        hp: "---"
    },

    white: {
        hc: "---",
        hp: "---"
    },

    red: {
        hc: "---",
        hp: "---"
    },

    subscribedPower: "---"
};


/*
 * Éléments HTML
 */

const startDateInput = document.getElementById("start-date");
const endDateInput = document.getElementById("end-date");
const applyButton = document.getElementById("apply-period");


/*
 * Formatage des dates
 */

function formatDate(date) {

    if (!date) {
        return "--/--/----";
    }

    const parts = date.split("-");

    if (parts.length !== 3) {
        return "--/--/----";
    }

    return `${parts[2]}/${parts[1]}/${parts[0]}`;
}


/*
 * Affichage des tarifs
 */

function displayTariffs() {

    document.getElementById("blue-hc").textContent =
        tariffData.blue.hc;

    document.getElementById("blue-hp").textContent =
        tariffData.blue.hp;


    document.getElementById("white-hc").textContent =
        tariffData.white.hc;

    document.getElementById("white-hp").textContent =
        tariffData.white.hp;


    document.getElementById("red-hc").textContent =
        tariffData.red.hc;

    document.getElementById("red-hp").textContent =
        tariffData.red.hp;


    document.getElementById("subscribed-power").textContent =
        tariffData.subscribedPower;
}


/*
 * Mise à jour de la période affichée
 */

function updatePeriod() {

    const startDate = startDateInput.value;
    const endDate = endDateInput.value;

    if (!startDate || !endDate) {
        return;
    }

    if (startDate > endDate) {
        alert("La date de début doit être antérieure à la date de fin.");
        return;
    }

    const periodText =
        `${formatDate(startDate)} → ${formatDate(endDate)}`;

    document.getElementById("selected-period").textContent =
        periodText;

    document.getElementById("chart-period").textContent =
        periodText;


    /*
     * Plus tard :
     *
     * fetch(`/api/tarifs?debut=${startDate}&fin=${endDate}`)
     *
     * permettra de récupérer les tarifs historiques
     * depuis l'API Symfony.
     */

    displayTariffs();
}


/*
 * Bouton "Afficher"
 */

applyButton.addEventListener("click", updatePeriod);


/*
 * Dates par défaut :
 * début de l'année jusqu'à aujourd'hui.
 */

const today = new Date();

const year = today.getFullYear();

const firstDayOfYear =
    `${year}-01-01`;

const currentDate =
    today.toISOString().split("T")[0];

startDateInput.value = firstDayOfYear;
endDateInput.value = currentDate;


/*
 * Date de dernière mise à jour temporaire.
 */

document.getElementById("last-update").textContent =
    today.toLocaleDateString("fr-FR");


/*
 * Initialisation
 */

updatePeriod();