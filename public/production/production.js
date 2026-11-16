

const API_LINK_JOUR = "/api/jour";

const API_LINK_TARIFS = "/api/grille-tarifaire";

let dataPeriod = [];

let tarifPeriod = [];





async function getDataPeriod(dateDebut, dateFin) {
    try {
        const response = await fetch(`${API_LINK_JOUR}/period?dateDebut=${dateDebut}&dateFin=${dateFin}`)
        const resultat = await response.json();

        if (!response.ok) {
            throw new Error(
                resultat.message
                || resultat.error
                || `Erreur HTTP ${response.status}`
            );
        }
        if (resultat.length === 0) {
            console.log("La liste des jours est vide");
        } else {
            dataPeriod = resultat;
            console.log(dataPeriod);
        }
    } catch (error) {
        console.error("Erreur lors de la récupération des imports :", error);
    }
}

async function getTarifsPeriod(dateDebut, dateFin) {
    try {
        const response = await fetch(`${API_LINK_TARIFS}/period?dateDebut=${dateDebut}&dateFin=${dateFin}`)
        const resultat = await response.json();

        if (!response.ok) {
            throw new Error(
                resultat.message
                || resultat.error
                || `Erreur HTTP ${response.status}`
            );
        }
        if (resultat.length === 0) {
            console.log("La liste de tarifs est vide");
        } else {
            tarifPeriod = resultat;
            console.log(tarifPeriod);
        }
    } catch (error) {
        console.error("Erreur lors de la récupération des imports :", error);
    }
}


document.getElementById('apply-period').addEventListener("click", async () => {

    var dateDebut = document.getElementById('start-date').value;
    var dateFin = document.getElementById('end-date').value;

    await getDataPeriod(dateDebut, dateFin);
    await getTarifsPeriod(dateDebut, dateFin);

    dateDebut = new Date(dateDebut).toLocaleDateString('fr-FR');
    dateFin = new Date(dateFin).toLocaleDateString('fr-FR');

    document.getElementById('chart-period').textContent = `${dateDebut} → ${dateFin}`;
    document.getElementById('selected-period').textContent = `${dateDebut} → ${dateFin}`;

    globalView();
    rentabilityView();
    autonomyView();
    ajouterPuissances();
})



document.querySelectorAll('.tab').forEach((btn) => btn.addEventListener('click', () => {
    document.querySelectorAll('.tab').forEach((b) => b.classList.toggle('active', b === btn));
    document.querySelectorAll('.tab-panel').forEach((p) => p.classList.toggle('active', p.id === 'panel-' + btn.dataset.tab));
}));


function calculateData() {

}


// Fonction pour la vue globale
function globalView() {

    chartGlobalView.data.datasets.forEach(dataset => { dataset.data = []; });

    let totalProductionElement = document.getElementById('production');
    let totalConsommationElement = document.getElementById('consumption');
    let totalAutoConsommationElement = document.getElementById('self-kwh');
    let totalImportElement = document.getElementById('imported');
    let averageDailyProductionElement = document.getElementById('average-production');
    let maximumDayProductionElement = document.getElementById('max-production');
    let bestProductionDayElement = document.getElementById('best-day');
    let autoConsommationElement = document.getElementById('self-consumption');

    var totalProduction = 0;
    var totalConsommation = 0;
    var totalImport = 0;
    var totalAutoConsommation = 0;

    let dailyProduction = 0;
    let dailyConsommation = 0;
    let dailyImport = 0;
    let dailyAutoConsommation = 0;

    let maxProduction = 0;
    let bestProductionDay = new Date();

    dataPeriod.forEach(element => {

        dailyProduction = Number(element.production.energieHC ?? 0) + Number(element.production.energieHP ?? 0);
        dailyConsommation = Number(element.consommation.energieConsommeeHC ?? 0) + Number(element.consommation.energieConsommeeHP ?? 0);
        dailyImport = Number(element.reseau.energieImporteeHC ?? 0) + Number(element.reseau.energieImporteeHP ?? 0);
        dailyAutoConsommation = Math.trunc((dailyConsommation - dailyImport) * 100) / 100;

        totalProduction += dailyProduction;
        totalConsommation += dailyConsommation;
        totalImport += dailyImport;

        chartGlobalView.data.labels.push(element.dateJour);
        chartGlobalView.data.datasets[0].data.push(dailyProduction);
        chartGlobalView.data.datasets[1].data.push(dailyConsommation);
        chartGlobalView.data.datasets[2].data.push(dailyImport);
        chartGlobalView.data.datasets[3].data.push(dailyAutoConsommation);

        if (dailyProduction > maxProduction) {
            maxProduction = dailyProduction;
            bestProductionDay = new Date(element.dateJour);
        }
    });

    chartGlobalView.update();

    totalProduction = Math.trunc(totalProduction * 100) / 100;
    totalConsommation = Math.trunc(totalConsommation * 100) / 100;
    totalImport = Math.trunc(totalImport * 100) / 100;
    totalAutoConsommation = Math.trunc((totalConsommation - totalImport) * 100) / 100;


    totalProductionElement.textContent = totalProduction;
    totalConsommationElement.textContent = totalConsommation;
    totalAutoConsommationElement.textContent = totalAutoConsommation;
    totalImportElement.textContent = totalImport;

    averageDailyProductionElement.textContent = Math.trunc((totalProduction / dataPeriod.length) * 100) / 100;
    maximumDayProductionElement.textContent = maxProduction;
    bestProductionDayElement.textContent = bestProductionDay.toLocaleDateString('fr-FR');
    autoConsommationElement.textContent = Math.trunc((totalProduction / totalConsommation) * 100 * 100) / 100;

}


const chartGlobalView = new Chart(document.getElementById('chart-overview'), {
    type: 'line',
    data: {
        labels: [],
        datasets: [
            { label: 'Production', data: [], borderColor: '#e0a100', backgroundColor: '#e0a100', borderWidth: 2, pointRadius: 0, tension: 0 },
            { label: 'Consommation', data: [], borderColor: '#2b6cb0', backgroundColor: '#2b6cb0', borderWidth: 2, pointRadius: 0, tension: 0 },
            { label: 'Importée', data: [], borderColor: '#b3402f', backgroundColor: '#b3402f', borderWidth: 2, pointRadius: 0, tension: 0 },
            { label: 'Énergie autoconsommée', data: [], borderColor: '#2a7d46', backgroundColor: '#2a7d46', borderWidth: 2, pointRadius: 0 }
        ]
    },
    options: {
        responsive: true,
        interaction: { mode: 'index', intersect: false },
        plugins: {
            legend: { position: 'bottom' },
            tooltip: { callbacks: { label: (c) => `${c.dataset.label} : ${c.parsed.y.toFixed(2)} kWh` } }
        },
        scales: {
            y: { beginAtZero: true, title: { display: true, text: 'kWh' } },
            x: { ticks: { maxTicksLimit: 6 } }
        }
    }
});


// Fonction pour la vue de rentabilite

const listePuissanceRenta = document.getElementById('liste-puiss-renta');

function rentabilityView(puissance) {

    let labels = [];
    let dataAutoConsommation = [];
    let dataReseau = [];
    let dataVE = [];
    let dataCumulSans = [];
    let dataCumulAvec = [];

    let totalAutoConsommation = 0;
    let totalReseau = 0;
    let totalVE = 0;

    cumulSans = 0;
    cumulAvec = 0;


    dataPeriod.forEach(element => {

        dailyProductionHC = Number(element.production.energieHC ?? 0);
        dailyConsommationHC = Number(element.consommation.energieConsommeeHC ?? 0);
        dailyImportHC = Number(element.reseau.energieImporteeHC ?? 0);
        dailyAutoConsommationHC = Math.trunc((dailyConsommationHC - dailyImportHC) * 100) / 100;


        dailyProductionHP = Number(element.production.energieHP ?? 0);
        dailyConsommationHP = Number(element.consommation.energieConsommeeHP ?? 0);
        dailyImportHP = Number(element.reseau.energieImporteeHP ?? 0);
        dailyAutoConsommationHP = Math.trunc((dailyConsommationHP - dailyImportHP) * 100) / 100;
        dailyVEHC = Number(element.vehiculeElectrique.energieHC ?? 0);
        dailyVEHP = Number(element.vehiculeElectrique.energieHP ?? 0);

        tarifHC = Number(trouverTarif(element.dateJour, element.couleur, Number(puissance), "HC"));
        tarifHP = Number(trouverTarif(element.dateJour, element.couleur, Number(puissance), "HP"));


        dailyCostProd = Number(dailyProductionHC * tarifHC) + Number(dailyProductionHP * tarifHP);
        dailyCostCosnommation = Number(dailyConsommationHC * tarifHC) + Number(dailyConsommationHP * tarifHP);
        dailyCostImport = Number(dailyImportHC * tarifHC) + Number(dailyImportHP * tarifHP);
        dailyCostAutoConsommation = Number(dailyAutoConsommationHC * tarifHC) + Number(dailyAutoConsommationHP * tarifHP);
        dailyCostVE = Number(dailyVEHC * tarifHC) + Number(dailyVEHP * tarifHP);


        // Graphique journalier (barres empilées)
        labels.push(element.dateJour);
        dataAutoConsommation.push(Math.trunc(dailyCostAutoConsommation * 100) / 100);
        dataReseau.push(Math.trunc(dailyCostImport * 100) / 100);
        dataVE.push(Math.trunc(dailyCostVE * 100) / 100);

        // Totaux pour le doughnut
        totalAutoConsommation += dailyCostAutoConsommation;
        totalReseau += dailyCostImport;
        totalVE += dailyCostVE;

        // Facture cumulée : sans autoproduction = tout acheté au réseau, avec = seulement l'import
        cumulSans += dailyCostCosnommation;
        cumulAvec += dailyCostImport;
        dataCumulSans.push(Math.trunc(cumulSans * 100) / 100);
        dataCumulAvec.push(Math.trunc(cumulAvec * 100) / 100);


    });

    // Graphique : valeur de la consommation par jour
    chartDailyIndividualPrice.data.labels = labels;
    chartDailyIndividualPrice.data.datasets[0].data = dataAutoConsommation;
    chartDailyIndividualPrice.data.datasets[1].data = dataReseau;
    chartDailyIndividualPrice.data.datasets[2].data = dataVE;
    chartDailyIndividualPrice.update();

    // Graphique : répartition globale
    chartGlobalIndividualPrice.data.datasets[0].data = [
        Math.trunc(totalAutoConsommation * 100) / 100,
        Math.trunc(totalReseau * 100) / 100,
        Math.trunc(totalVE * 100) / 100
    ];
    chartGlobalIndividualPrice.update();

    // Graphique : facture cumulée
    chartCumulativeCost.data.labels = labels;
    chartCumulativeCost.data.datasets[0].data = dataCumulSans;
    chartCumulativeCost.data.datasets[1].data = dataCumulAvec;
    chartCumulativeCost.update();

}

function trouverTarif(dateRecherchee, couleurJour, puissance, typeHeure) {
    const date = new Date(dateRecherchee);

    const element = tarifPeriod.find(element => {
        const debut = new Date(element.dateDebut);
        const fin = new Date(element.dateFin);

        return date >= debut && date <= fin
            && element.couleur === couleurJour && Number(element.puissance) === puissance
            && element.type === typeHeure;
    });

    return element ? Number(element.tarif) : null;
}

function ajouterPuissances() {

    puissances = [...new Set(tarifPeriod.map(t => t.puissance))].filter(p => p !== null).sort((a, b) => a - b);

    puissances.forEach(element => {
        const option = document.createElement("option");

        option.value = element;
        option.textContent = `${element} kVA`;

        listePuissanceRenta.appendChild(option);
    });

}

listePuissanceRenta.addEventListener("change", (event) => {
    puissanceChoisie = Number(event.target.value);
    rentabilityView(puissanceChoisie)
});


const chartDailyIndividualPrice = new Chart(document.getElementById('chart-profit-split'), {
    type: 'bar',
    data: {
        labels: [],
        datasets: [
            { label: 'Autoconsommation', data: [], backgroundColor: '#e0a100' },
            { label: 'Réseau', data: [], backgroundColor: '#b3402f' },
            { label: 'VE', data: [], backgroundColor: '#7c4dab' }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            title: { display: true, text: 'Valeur de la consommation par jour' },
            legend: { position: 'bottom' },
            tooltip: { callbacks: { label: (c) => `${c.dataset.label} : ${c.parsed.y.toFixed(2)} €` } }
        },
        scales: {
            x: { stacked: true, ticks: { maxTicksLimit: 6 } },
            y: { stacked: true, beginAtZero: true, title: { display: true, text: '€' } }
        }
    }
});

const chartGlobalIndividualPrice = new Chart(document.getElementById('chart-mix-profit'), {
    type: 'doughnut',
    data: {
        labels: ['Autoconsommation', 'Réseau', 'VE'],
        datasets: [
            { data: [], backgroundColor: ['#e0a100', '#b3402f', '#7c4dab'] }
        ]
    },
    options: {
        responsive: true,
        cutout: '65%',
        plugins: {
            title: { display: true, text: 'Répartition de la valeur de la consommation' },
            legend: { position: 'bottom' },
            tooltip: { callbacks: { label: (c) => `${c.label} : ${c.parsed.toFixed(2)} €` } }
        }
    }
});

let cumulSans = 0, cumulAvec = 0;

const chartCumulativeCost = new Chart(document.getElementById('chart-cumul'), {
    type: 'line',
    data: {
        labels: [],
        datasets: [
            { label: 'Sans autoproduction', data: [], borderColor: '#b3402f', backgroundColor: '#b3402f', borderWidth: 2, pointRadius: 0 },
            { label: 'Avec autoproduction', data: [], borderColor: '#2a7d46', backgroundColor: '#2a7d46', borderWidth: 2, pointRadius: 0 }
        ]
    },
    options: {
        responsive: true,
        interaction: { mode: 'index', intersect: false },
        plugins: {
            title: { display: true, text: 'Facture cumulée' },
            legend: { position: 'bottom' },
            tooltip: { callbacks: { label: (c) => `${c.dataset.label} : ${c.parsed.y.toFixed(2)} €` } }
        },
        scales: {
            x: { ticks: { maxTicksLimit: 6 } },
            y: { beginAtZero: true, title: { display: true, text: '€' } }
        }
    }
});

// Fonction pour la vue autonomie
function autonomyView() {

}
