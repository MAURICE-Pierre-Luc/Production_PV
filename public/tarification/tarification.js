/*
 * Configuration de l'API
 */

const API_URL = "/api/grille-tarifaire";


const canvas = document.getElementById('tariff-chart');
const listePuissances =  document.getElementById('subscribed-power');
const dateMAJ = document.getElementById('last-update');
const spanPeriode = document.getElementById('chart-period')

let puissanceChoisie = 6;   // variable modifiable (kVA)
let tousLesTarifs = [];     // on garde les données pour ne pas refaire l'appel API

// Associe "couleur + type" à l'index du dataset du graphe
const INDEX_DATASETS = {
    'BLEU_HC': 0,
    'BLEU_HP': 1,
    'BLANC_HC': 2,
    'BLANC_HP': 3,
    'ROUGE_HC': 4,
    'ROUGE_HP': 5
};

const COULEURS_ORIGINALES = {};

/*
 * Recuperation des tarifs
 */

getTarifs();

async function getTarifs(){
    try{
        const response = await fetch(`${API_URL}`)
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

            tousLesTarifs = resultat;
            afficherTarifs(puissanceChoisie);
            
        }
    } catch (error)  {
        console.error("Erreur lors de la récupération des imports :", error);
    }
}


function afficherTarifs(puissance) {
    // 1. Filtrer sur la puissance choisie
    const tarifs = tousLesTarifs.filter(t => t.puissance === puissance);

    // 2. Regrouper par dateDebut : { "2025-01-01": { bleu_HC: 0.1296, ... }, ... }
    const parDate = {};

    tarifs.forEach(t => {
        const cle = `${t.couleur.toUpperCase()}_${t.type.toUpperCase()}`;

        if (!(cle in INDEX_DATASETS)){
            
            console.log(`Combinaison inconnue ${cle}`)
            return; // combinaison inconnue : on ignore
        }
        
        if (!parDate[t.dateDebut]) {
            parDate[t.dateDebut] = {};
        }
        // Plusieurs plages possibles pour une même couleur/type : le tarif est le même, on écrase
        parDate[t.dateDebut][cle] = parseFloat(t.tarif);
    });

    // 3. Trier les dates (le format Y-m-d se trie correctement en texte)
    const dates = Object.keys(parDate).sort();

    // 4. Remplir le graphe
    chart.data.labels = dates;

    chart.data.datasets.forEach((dataset, index) => {
        const cle = Object.keys(INDEX_DATASETS).find(k => INDEX_DATASETS[k] === index);
        dataset.data = dates.map(date => parDate[date][cle] ?? null);
    });

    chart.update();
}



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


// Associe chaque courbe à sa couleur Tempo
function trouverGroupe(index) {
    const cle = Object.keys(INDEX_DATASETS)
        .find(cle => INDEX_DATASETS[cle] === index);

    if (!cle) {
        return [];
    }

    const couleur = cle.split('_')[0];

    return Object.keys(INDEX_DATASETS)
        .filter(cle => cle.startsWith(couleur + '_'))
        .map(cle => INDEX_DATASETS[cle]);
}

// Met en évidence une ou plusieurs courbes
function mettreEnEvidence(chart, indicesActifs) {
    chart.data.datasets.forEach((dataset, index) => {
        const actif = indicesActifs === null
            || indicesActifs.includes(index);

        const couleur = COULEURS_ORIGINALES[index];

        dataset.borderColor = actif
            ? couleur
            : couleur + '33';

        dataset.borderWidth = actif ? 4 : 2;
    });

    chart.update('none');
}

const chart = new Chart(canvas, {
    type: 'line',

    data: {
        labels: [],

        datasets: [
            // Bleu
            {
                label: 'Bleu HC',
                data: [],
                borderColor: '#4385d8',
                tension: 0.2
            },
            {
                label: 'Bleu HP',
                data: [],
                borderColor: '#4385d8',
                borderDash: [5, 5],
                tension: 0.2
            },

            // Blanc
            {
                label: 'Blanc HC',
                data: [],
                borderColor: '#999999',
                tension: 0.2
            },
            {
                label: 'Blanc HP',
                data: [],
                borderColor: '#999999',
                borderDash: [5, 5],
                tension: 0.2
            },

            // Rouge
            {
                label: 'Rouge HC',
                data: [],
                borderColor: '#d9534f',
                tension: 0.2
            },
            {
                label: 'Rouge HP',
                data: [],
                borderColor: '#d9534f',
                borderDash: [5, 5],
                tension: 0.2
            }
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
        },
        interaction: {
            mode: 'nearest',
            intersect: false,
            axis: 'xy'
        },
    }
});

chart.data.datasets.forEach((dataset, index) => {
    COULEURS_ORIGINALES[index] = dataset.borderColor;
});






const puissances = [...new Set(tousLesTarifs.map(g => g.puissance))].filter(p => p !== null).sort((a, b) => a - b);

const dateMin = tousLesTarifs.reduce((min, g) => !min || g.dateDebut < min ? g.dateDebut : min, null);

const dateMax = tousLesTarifs.some(g => g.dateFin === null) ? null : tousLesTarifs.reduce((max, g) => !max || g.dateFin > max ? g.dateFin : max, null);


puissances.forEach(element => {
    const option = document.createElement("option");

    option.value = element;
    option.textContent = `${element} kVA`;

    listePuissances.appendChild(option);
});

listePuissances.addEventListener("change", (event) => {
    puissanceChoisie = Number(event.target.value);
    afficherTarifs(puissanceChoisie);
});


dateMAJ.textContent = formatDate(dateMax);

spanPeriode.textContent = `${formatDate(dateMin)} → ${formatDate(dateMax)}`;