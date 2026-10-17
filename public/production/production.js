

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
            
            dataPeriod = resultat;
            console.log(dataPeriod);

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

            tarifPeriod = resultat;
            console.log(tarifPeriod);
            
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

    globalview();

})


function globalview(){
    let totalProductionElement = document.getElementById('production');
    let totalConsommationElement = document.getElementById('consumption');
    let totalAutoConsomationElement = document.getElementById('self-kwh');
    let totalImporteElement = document.getElementById('imported');

    var totalProduction = 0;
    var totalConsommation = 0;
    var totalImporte = 0;
    var totalAutoConsomation = 0;

    console.log(dataPeriod['production']['energieHC']);
    console.log(dataPeriod['production']['energieConsommeeHC']);
    console.log(dataPeriod['reseau']['energieImporteeHC']);

    dataPeriod.forEach(element => {
        
        totalProduction =+ (element['production']['energieHC'] + element['production']['energieHP']);
        totalConsommation =+ (element['production']['energieConsommeeHC'] + element['production']['energieConsommeeHP']);
        totalImporte =+ (element['reseau']['energieImporteeHC'] + element['reseau']['energieImporteeHP'])
        
    });

    console.log(totalProduction);
    console.log(totalConsommation);
    console.log(totalImporte);

    totalAutoConsomation = totalConsommation - totalImporte;

    totalProductionElement.textContent = totalProduction;
    totalConsommationElement.textContent = totalConsommation;
    totalAutoConsomationElement.textContent = totalAutoConsomation;
    totalImporteElement.textContent = totalImporte;


}

