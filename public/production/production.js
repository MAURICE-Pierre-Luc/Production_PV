

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

    globalview();

})


function globalview() {

    chartGlobalView.data.datasets.forEach(dataset => { dataset.data = []; });

    let totalProductionElement = document.getElementById('production');
    let totalConsommationElement = document.getElementById('consumption');
    let totalAutoConsommationElement = document.getElementById('self-kwh');
    let totalImportElement = document.getElementById('imported');

    var totalProduction = 0;
    var totalConsommation = 0;
    var totalImport = 0;
    var totalAutoConsommation = 0;

    let dailyProduction = 0;
    let dailyConsommation = 0;
    let dailyImport = 0;
    let dailyAutoConsommation = 0;

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
        maintainAspectRatio: false,

        // Survol d'une ligne du graphique
        onHover: (event, elements, chart) => {
            if (elements.length > 0) {
                const index = elements[0].datasetIndex;
                mettreEnEvidence(chart, trouverGroupe(index));
            } else {
                mettreEnEvidence(chart, null);
            }
        },

        plugins: {
            legend: {
                onHover: (event, item, legend) => {
                    mettreEnEvidence(
                        legend.chart,
                        trouverGroupe(item.datasetIndex)
                    );
                },

                onLeave: (event, item, legend) => {
                    mettreEnEvidence(legend.chart, null);
                }
            }
        },

        scales: {
            y: { beginAtZero: true, title: { display: true, text: 'kWh' } },
            x: { ticks: { maxTicksLimit: 6 } }
        },
        
        interaction: {
            mode: 'nearest',
            intersect: false,
            axis: 'xy'
        },
    }
});
