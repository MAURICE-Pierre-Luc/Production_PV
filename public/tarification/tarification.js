/*
 * Configuration de l'API
 */

const API_URL = "/api/grille-tarifaire";


/*
 * Recuperation des tarifs
 */


async function getTarifs(){

    try{
        const response = await fetch(`${API_URL}`)

        // Lecture de la réponse JSON de Symfony.
        const resultat = await response.json();

        if (!response.ok) {
            throw new Error(
                resultat.message
                || resultat.error
                || `Erreur HTTP ${response.status}`
            );
        }

        

        if(resultat.length === 0){
            console.log("La liste de tarifs est vide");
        }else{

            console.log(resultat);

            afficherTarifs(resultat);
            
        }

    } catch (error)  {
        console.error("Erreur lors de la récupération des imports :", error);
    }


}

getTarifs();


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

    return `${parts[0]}/${parts[1]}/${parts[2]}`;
}


/*
 * Affichage des tarifs
 */

function displayTariffs() {

    document.getElementById("blue-hc").textContent = tariffData.blue.hc;

    document.getElementById("blue-hp").textContent = tariffData.blue.hp;


    document.getElementById("white-hc").textContent = tariffData.white.hc;

    document.getElementById("white-hp").textContent = tariffData.white.hp;


    document.getElementById("red-hc").textContent = tariffData.red.hc;

    document.getElementById("red-hp").textContent = tariffData.red.hp;


    document.getElementById("subscribed-power").textContent = tariffData.subscribedPower;
}


// /*
//  * Mise à jour de la période affichée
//  */

// async function updatePeriod() {

//     const startDate = startDateInput.value;
//     const endDate = endDateInput.value;

//     if (!startDate || !endDate) {
//         return;
//     }

//     if (startDate > endDate) {
//         alert("La date de début doit être antérieure à la date de fin.");
//         return;
//     }

//     const periodText = `${formatDate(startDate)} → ${formatDate(endDate)}`;


//     displayTariffs();
// }

// /*
//  * Initialisation
//  */

// updatePeriod();











const canvas = document.getElementById('tariff-chart');

const chart = new Chart(canvas, {
    type: 'line',

    data: {
        labels: [],

        datasets: [
            {
                label: 'Tarif bleu HC',
                data: [],
                borderColor: '#4385d8',
                backgroundColor: '#4385d8',
                tension: 0.2,
                spanGaps: true
            },
            {
                label: 'Tarif blanc HC',
                data: [],
                borderColor: '#999999',
                backgroundColor: '#999999',
                tension: 0.2,
                spanGaps: true
            },
            {
                label: 'Tarif rouge HC',
                data: [],
                borderColor: '#d9534f',
                backgroundColor: '#d9534f',
                tension: 0.2,
                spanGaps: true
            }
        ]
    },

    options: {
        responsive: true,
        maintainAspectRatio: false,

        scales: {
            y: {
                beginAtZero: true,

                title: {
                    display: true,
                    text: 'Prix (€/kWh)'
                }
            },

            x: {
                title: {
                    display: true,
                    text: 'Date'
                }
            }
        }
    }
});

function afficherTarifs(tarifs) {
    // Regroupe par date de début : { '2026-01-01': { bleu: 0.1, blanc: 0.2, rouge: 0.3 } }
    const parDate = {};

    tarifs.filter(t => t.type === 'HC').forEach(t => {
            if (!parDate[t.dateDebut]) {
                parDate[t.dateDebut] = {};
            }
            // On garde la première valeur rencontrée (puissance ignorée)
            if (parDate[t.dateDebut][t.couleur] === undefined) {
                parDate[t.dateDebut][t.couleur] = Number(t.tarif);
            }
        });

    // Les dates au format Y-m-d se trient correctement par ordre alphabétique
    const dates = Object.keys(parDate).sort();

    chart.data.labels = dates;
    chart.data.datasets[0].data = dates.map(d => parDate[d].bleu  ?? null);
    chart.data.datasets[1].data = dates.map(d => parDate[d].blanc ?? null);
    chart.data.datasets[2].data = dates.map(d => parDate[d].rouge ?? null);

    console.log("test")

    chart.update();
}


