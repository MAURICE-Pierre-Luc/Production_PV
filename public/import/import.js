/*
 * Vérification de la connexion
 */

if (sessionStorage.getItem("loggedIn") !== "true") {

    window.location.href = "../login/login.html";
}


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


    /*
     * Vérification de l'extension.
     */

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
 * Import
 */

importButton.addEventListener("click", function () {

    const file = csvFile.files[0];


    if (!file) {

        fileError.textContent =
            "Veuillez sélectionner un fichier CSV.";

        fileError.classList.add("visible");

        return;
    }


    fileError.classList.remove("visible");


    /*
     * Pour le moment, aucune requête n'est envoyée.
     *
     * Plus tard, ici on enverra le fichier à Symfony :
     *
     * const formData = new FormData();
     * formData.append("file", file);
     *
     * fetch("/api/import", {
     *     method: "POST",
     *     body: formData
     * });
     */

    importStatus.textContent =
        "Le fichier est prêt à être envoyé au serveur.";

    importStatus.classList.add("visible");
});