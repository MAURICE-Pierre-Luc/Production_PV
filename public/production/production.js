

const API_LINK_JOUR = "/api/jour";

const API_LINK_TARIFS = "/api/grille-tarifaire";

let dataPeriod = [];

let tarifPeriod = [];



async function getDataPeriod(dateDebut, dateFin){

    try{
        const response = await fetch(`${API_LINK_JOUR}/period?dateDebut=${dateDebut}&dateFin=${dateFin}`)
        const resultat = await response.json();

        if (!response.ok) {
            throw new Error(
                resultat.message
                || resultat.error
                || `Erreur HTTP ${response.status}`
            );
        }
        if(resultat.length === 0){
            console.log("La liste des jours est vide");
        }else{
            
            console.log(resultat);
            dataPeriod = resultat;

        }
    } catch (error)  {
        console.error("Erreur lors de la récupération des imports :", error);
    }

}

async function getTarifsPeriod(dateDebut, dateFin){

    try{
        const response = await fetch(`${API_LINK_TARIFS}/period?dateDebut=${dateDebut}&dateFin=${dateFin}`)
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
            tarifPeriod = resultat;
            
        }
    } catch (error)  {
        console.error("Erreur lors de la récupération des imports :", error);
    }

}


document.getElementById('apply-period').addEventListener("click", async () => {
    
    var dateDebut = document.getElementById('start-date').value;
    var dateFin = document.getElementById('end-date').value;

    getDataPeriod(dateDebut, dateFin);
    getTarifsPeriod(dateDebut, dateFin);
})


function globalview(){
    let totalProductionElement = document.getElementById('production');
    let totalConsommationElement = document.getElementById('consumption');
    let totalAutoConsomationElement = document.getElementById('self-kwh');
    let totalImporteElement = document.getElementById('imported');

    var totalProduction = 0;

    var totalConsommation = 0;

    dataPeriod.forEach(element => {
        
        totalProduction =+ (element['production']['energieHC'] + element['production']['energieHP']);

        totalConsommation =+ (element['production']['energieHC'] + element['production']['energieHP']);

    });
}

