/*
 * Données temporaires.
 *
 * Ces valeurs seront remplacées plus tard par les données
 * récupérées depuis l'API Symfony.
 */

const installationData = {
    totalProduction: "---",
    yearProduction: "---",
    maxProduction: "---",
    totalConsumption: "---",

    installedPower: "---",
    panelCount: "---",
    installationDate: "--/--/----",
    orientation: "---",
    inclination: "---",
    batteryCapacity: "---",

    selfConsumption: "---",
    energyExported: "---",
    evEnergy: "---"
};


/*
 * Affichage des données.
 */

document.getElementById("total-production").textContent =
    installationData.totalProduction;

document.getElementById("year-production").textContent =
    installationData.yearProduction;

document.getElementById("max-production").textContent =
    installationData.maxProduction;

document.getElementById("total-consumption").textContent =
    installationData.totalConsumption;


document.getElementById("installed-power").textContent =
    installationData.installedPower;

document.getElementById("panel-count").textContent =
    installationData.panelCount;

document.getElementById("installation-date").textContent =
    installationData.installationDate;

document.getElementById("orientation").textContent =
    installationData.orientation;

document.getElementById("inclination").textContent =
    installationData.inclination;

document.getElementById("battery-capacity").textContent =
    installationData.batteryCapacity;


document.getElementById("self-consumption").textContent =
    installationData.selfConsumption;

document.getElementById("energy-exported").textContent =
    installationData.energyExported;

document.getElementById("ev-energy").textContent =
    installationData.evEnergy;


/*
 * Date de dernière actualisation.
 *
 * Temporairement, on utilise la date du chargement de la page.
 * Plus tard, cette valeur pourra venir de l'API.
 */

const now = new Date();

const formattedDate = now.toLocaleString("fr-FR", {
    dateStyle: "short",
    timeStyle: "short"
});

document.getElementById("last-update").textContent = formattedDate;