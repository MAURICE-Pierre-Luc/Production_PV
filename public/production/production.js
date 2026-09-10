/*
 * Données temporaires.
 *
 * Elles seront remplacées par les données
 * récupérées depuis l'API Symfony.
 */

const periodData = {
    production: "---",
    consumption: "---",
    exported: "---",
    imported: "---",

    averageProduction: "---",
    bestDay: "--/--/----",
    maxProduction: "---",
    selfConsumption: "---"
};


/*
 * Éléments HTML
 */

const startDateInput = document.getElementById("start-date");
const endDateInput = document.getElementById("end-date");
const applyButton = document.getElementById("apply-period");


/*
 * Formatage d'une date pour l'affichage.
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
 * Affichage des données.
 */

function displayData() {

    document.getElementById("production").textContent =
        periodData.production;

    document.getElementById("consumption").textContent =
        periodData.consumption;

    document.getElementById("exported").textContent =
        periodData.exported;

    document.getElementById("imported").textContent =
        periodData.imported;


    document.getElementById("average-production").textContent =
        periodData.averageProduction;

    document.getElementById("best-day").textContent =
        periodData.bestDay;

    document.getElementById("max-production").textContent =
        periodData.maxProduction;

    document.getElementById("self-consumption").textContent =
        periodData.selfConsumption;
}


/*
 * Mise à jour de la période affichée.
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
     * Plus tard, c'est ici que l'on pourra faire
     * la requête vers l'API Symfony.
     *
     * Exemple :
     *
     * fetch(`/api/production?debut=${startDate}&fin=${endDate}`)
     *
     * puis mettre à jour periodData avec la réponse.
     */

    displayData();
}


/*
 * Bouton "Afficher"
 */

applyButton.addEventListener("click", updatePeriod);


/*
 * Dates par défaut.
 *
 * Pour le prototype, on affiche le mois en cours.
 */

const today = new Date();

const year = today.getFullYear();
const month = String(today.getMonth() + 1).padStart(2, "0");

startDateInput.value = `${year}-${month}-01`;
endDateInput.value = today.toISOString().split("T")[0];

updatePeriod();