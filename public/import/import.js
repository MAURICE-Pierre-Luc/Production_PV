/*
 * Vérification de la connexion
 */

if (sessionStorage.getItem("loggedIn") !== "true") {
    window.location.href = "../login/login.html";
}


/*
 * Configuration de l'API
 */

// À adapter si la route de ton ImportController est différente.
const API_URL = "/api/import";


/*
 * Éléments HTML
 */

const csvFile = document.getElementById("csv-file");

const selectedFile = document.getElementById("selected-file");
const fileName = document.getElementById("file-name");
const fileSize = document.getElementById("file-size");

const removeFileButton = document.getElementById("remove-file");

const fileError = document.getElementById("file-error");
const importButton = document.getElementById("import-button");
const importStatus = document.getElementById("import-status");


/*
 * Sélection d'un fichier
 */

csvFile.addEventListener("change", function () {
    const file = csvFile.files[0];

    if (!file) {
        resetFile();
        return;
    }

    // Vérification de l'extension
    if (!file.name.toLowerCase().endsWith(".csv")) {
        resetFile();

        fileError.textContent =
            "Veuillez sélectionner un fichier CSV.";

        fileError.classList.add("visible");
        return;
    }

    fileError.classList.remove("visible");

    fileName.textContent = file.name;
    fileSize.textContent = formatFileSize(file.size);

    selectedFile.classList.remove("hidden");

    importStatus.classList.remove("visible");
    importStatus.textContent = "";
});


/*
 * Suppression du fichier
 */

removeFileButton.addEventListener("click", function () {
    resetFile();
});


function resetFile() {
    csvFile.value = "";

    selectedFile.classList.add("hidden");

    fileName.textContent = "---";
    fileSize.textContent = "---";

    fileError.classList.remove("visible");

    importStatus.classList.remove("visible");
    importStatus.textContent = "";

    importButton.disabled = false;
}


/*
 * Formatage de la taille du fichier
 */

function formatFileSize(size) {
    if (size < 1024) {
        return `${size} octets`;
    }

    if (size < 1024 * 1024) {
        return `${(size / 1024).toFixed(1)} Ko`;
    }

    return `${(size / (1024 * 1024)).toFixed(1)} Mo`;
}


/*
 * Import du CSV
 */

importButton.addEventListener("click", async function () {
    const file = csvFile.files[0];

    if (!file) {
        fileError.textContent =
            "Veuillez sélectionner un fichier CSV.";

        fileError.classList.add("visible");
        return;
    }

    if (!file.name.toLowerCase().endsWith(".csv")) {
        fileError.textContent =
            "Veuillez sélectionner un fichier CSV.";

        fileError.classList.add("visible");
        return;
    }

    fileError.classList.remove("visible");

    // Empêche les doubles envois pendant la requête.
    importButton.disabled = true;

    importStatus.textContent =
        "Envoi du fichier au serveur en cours...";

    importStatus.classList.add("visible");

    const formData = new FormData();
    formData.append("file", file);

    try {
        const response = await fetch(API_URL, {
            method: "POST",
            body: formData
        });

        // Lecture de la réponse JSON de Symfony.
        const resultat = await response.json();

        if (!response.ok) {
            throw new Error(
                resultat.message
                || resultat.error
                || `Erreur HTTP ${response.status}`
            );
        }

        // Symfony a accepté la demande d'import.
        importStatus.textContent =
            resultat.message
            || "Le fichier a été envoyé. Le traitement est en attente.";

        if (resultat.importId !== undefined) {
            importStatus.textContent +=
                ` Identifiant de l'import : ${resultat.importId}.`;
        }

        importStatus.classList.add("visible");

        // Le fichier a été envoyé : on peut le retirer de l'interface.
        csvFile.value = "";
        selectedFile.classList.add("hidden");

        fileName.textContent = "---";
        fileSize.textContent = "---";

    } catch (error) {
        console.error("Erreur lors de l'import :", error);

        importStatus.textContent =
            `Échec de l'import : ${error.message}`;

        importStatus.classList.add("visible");

    } finally {
        importButton.disabled = false;
    }
});


