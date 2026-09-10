const loginForm = document.getElementById("login-form");
const loginError = document.getElementById("login-error");


loginForm.addEventListener("submit", function (event) {

    event.preventDefault();

    const username = document.getElementById("username").value;
    const password = document.getElementById("password").value;


    /*
     * TEMPORAIRE
     *
     * Cette vérification sera remplacée par
     * l'authentification Symfony.
     */

    if (username === "admin" && password === "admin") {

        sessionStorage.setItem("loggedIn", "true");

        window.location.href = "../import/import.html";

    } else {

        loginError.classList.add("visible");
    }
});