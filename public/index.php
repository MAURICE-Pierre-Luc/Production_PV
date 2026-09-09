<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Solar Monitor - Accueil</title>

    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/home.css">
</head>

<body>

<header class="navbar">

    <a href="index.html" class="logo">
        <span class="logo-icon">☀</span>
        <span>Solar Monitor</span>
    </a>

    <nav class="nav-menu">
        <a href="index.html" class="nav-link active">Accueil</a>
        <a href="production.html" class="nav-link">Production</a>
        <a href="tarifs.html" class="nav-link">Tarifs</a>
        <a href="installation.html" class="nav-link">Installation</a>
    </nav>

    <a href="login.html" class="login-button">
        Connexion
    </a>

</header>


<main class="main-content">

    <!-- Présentation -->
    <section class="welcome">

        <div>
            <p class="eyebrow">TABLEAU DE BORD</p>

            <h1>
                Votre installation photovoltaïque
            </h1>

            <p class="subtitle">
                Suivez simplement la production et les performances
                de votre installation solaire.
            </p>
        </div>

        <div class="installation-status">
            <span class="status-dot"></span>
            Installation opérationnelle
        </div>

    </section>


    <!-- Statistiques principales -->
    <section class="stats-grid">

        <article class="stat-card">

            <div class="stat-header">
                <span>Production aujourd'hui</span>
                <span class="stat-icon">☀</span>
            </div>

            <div class="stat-value">
                <span class="placeholder">--.--</span>
                <span class="unit">kWh</span>
            </div>

            <p class="stat-description">
                Données du jour
            </p>

        </article>


        <article class="stat-card">

            <div class="stat-header">
                <span>Production annuelle</span>
                <span class="stat-icon">↗</span>
            </div>

            <div class="stat-value">
                <span class="placeholder">----</span>
                <span class="unit">kWh</span>
            </div>

            <p class="stat-description">
                Depuis le 1er janvier
            </p>

        </article>


        <article class="stat-card">

            <div class="stat-header">
                <span>Économies estimées</span>
                <span class="stat-icon">€</span>
            </div>

            <div class="stat-value">
                <span class="placeholder">---.--</span>
                <span class="unit">€</span>
            </div>

            <p class="stat-description">
                Cette année
            </p>

        </article>


        <article class="stat-card">

            <div class="stat-header">
                <span>Autoconsommation</span>
                <span class="stat-icon">⌂</span>
            </div>

            <div class="stat-value">
                <span class="placeholder">--</span>
                <span class="unit">%</span>
            </div>

            <p class="stat-description">
                Part de l'énergie produite
            </p>

        </article>

    </section>


    <!-- Production + installation -->
    <section class="dashboard-grid">

        <article class="panel">

            <div class="panel-header">

                <div>
                    <p class="panel-label">PRODUCTION</p>
                    <h2>Production récente</h2>
                </div>

                <a href="production.html" class="panel-link">
                    Voir les détails →
                </a>

            </div>

            <!-- Futur graphique -->
            <div class="chart-placeholder">

                <div class="chart-placeholder-content">

                    <span class="chart-icon">⌁</span>

                    <p>
                        Graphique de production
                    </p>

                    <span>
                        Les données de production seront affichées ici.
                    </span>

                </div>

            </div>

        </article>


        <article class="panel">

            <div class="panel-header">

                <div>
                    <p class="panel-label">INSTALLATION</p>
                    <h2>Informations</h2>
                </div>

                <a href="installation.html" class="panel-link">
                    Détails →
                </a>

            </div>


            <div class="installation-info">

                <div class="info-row">
                    <span>Puissance installée</span>
                    <strong>--.-- kWc</strong>
                </div>

                <div class="info-row">
                    <span>Nombre de panneaux</span>
                    <strong>--</strong>
                </div>

                <div class="info-row">
                    <span>Puissance maximale</span>
                    <strong>--.-- kW</strong>
                </div>

                <div class="info-row">
                    <span>Production totale</span>
                    <strong>---- kWh</strong>
                </div>

            </div>

        </article>

    </section>


    <!-- Tarif -->
    <section class="tariff-card">

        <div class="tariff-icon">
            €
        </div>

        <div class="tariff-content">

            <p class="panel-label">
                ÉLECTRICITÉ
            </p>

            <h2>
                Tarif actuel
            </h2>

            <p>
                Votre tarif actuel est
                <strong class="placeholder">---</strong>
                avec une période
                <strong class="placeholder">---</strong>.
            </p>

        </div>

        <a href="tarifs.html" class="secondary-button">
            Consulter les tarifs
        </a>

    </section>


    <!-- Accès rapides -->
    <section class="shortcuts">

        <div class="section-title">

            <p class="panel-label">
                ACCÈS RAPIDE
            </p>

            <h2>
                Explorer vos données
            </h2>

        </div>


        <div class="shortcut-grid">

            <a href="production.html" class="shortcut-card">

                <span class="shortcut-icon">
                    ▥
                </span>

                <div>
                    <h3>Production</h3>

                    <p>
                        Consultez votre production sur une période donnée.
                    </p>
                </div>

                <span class="shortcut-arrow">
                    →
                </span>

            </a>


            <a href="tarifs.html" class="shortcut-card">

                <span class="shortcut-icon">
                    €
                </span>

                <div>
                    <h3>Tarifs de l'électricité</h3>

                    <p>
                        Consultez l'évolution historique des tarifs.
                    </p>
                </div>

                <span class="shortcut-arrow">
                    →
                </span>

            </a>


            <a href="installation.html" class="shortcut-card">

                <span class="shortcut-icon">
                    ⚙
                </span>

                <div>
                    <h3>Installation</h3>

                    <p>
                        Retrouvez les informations de votre installation.
                    </p>
                </div>

                <span class="shortcut-arrow">
                    →
                </span>

            </a>

        </div>

    </section>

</main>


<footer class="footer">

    <span>
        Solar Monitor
    </span>

    <span>
        © 2026
    </span>

</footer>


<script src="js/home.js"></script>

</body>
</html>
