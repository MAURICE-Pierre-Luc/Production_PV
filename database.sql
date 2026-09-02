-- Table IMPORT
CREATE TABLE IMPORT (
    id_import SERIAL PRIMARY KEY,
    nom_fichier VARCHAR(255) NOT NULL,
    date_import TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_debut_donnees DATE,
    date_fin_donnees DATE
);

-- Table JOUR
CREATE TABLE JOUR (
    date_jour DATE PRIMARY KEY,
    couleur VARCHAR(50)
);

-- Table PRODUCTION_JOUR
CREATE TABLE PRODUCTION_JOUR (
    date_jour DATE PRIMARY KEY,
    energie_hc DECIMAL(15,3),
    energie_hp DECIMAL(15,3),
    puissance_max DECIMAL(10,2),
    heure_puissance_max TIME,
    FOREIGN KEY (date_jour) REFERENCES JOUR(date_jour)
);

-- Table CONSOMMATION_JOUR
CREATE TABLE CONSOMMATION_JOUR (
    date_jour DATE PRIMARY KEY,
    energie_importee_hc DECIMAL(15,3),
    energie_importee_hp DECIMAL(15,3),
    FOREIGN KEY (date_jour) REFERENCES JOUR(date_jour)
);

-- Table BATTERIE_JOUR
CREATE TABLE BATTERIE_JOUR (
    date_jour DATE PRIMARY KEY,
    soc_moyen DECIMAL(5,2),
    soc_min DECIMAL(5,2),
    soc_max DECIMAL(5,2),
    soh DECIMAL(5,2),
    temperature_min DECIMAL(5,2),
    temperature_max DECIMAL(5,2),
    nb_alarmes INTEGER,
    FOREIGN KEY (date_jour) REFERENCES JOUR(date_jour)
);

-- Table VE_JOUR
CREATE TABLE VE_JOUR (
    date_jour DATE PRIMARY KEY,
    energie_hc DECIMAL(15,3),
    energie_hp DECIMAL(15,3),
    duree_charge_minutes INTEGER,
    FOREIGN KEY (date_jour) REFERENCES JOUR(date_jour)
);

-- Table GRILLE_TARIFAIRE
CREATE TABLE GRILLE_TARIFAIRE (
    id_grille SERIAL PRIMARY KEY,
    date_debut DATE NOT NULL,
    date_fin DATE,
    couleur VARCHAR(50),
    deb_hc TIME,
    tarif_hc DECIMAL(10,4),
    deb_hp TIME,
    tarif_hp DECIMAL(10,4)
);

-- Table STATISTIQUE_GLOBALE
CREATE TABLE STATISTIQUE_GLOBALE (
    id_statistique SERIAL PRIMARY KEY,
    energie_totale_produite DECIMAL(15,3),
    energie_totale_importee DECIMAL(15,3),
    energie_totale_ve DECIMAL(15,3),
    soc_max_observe DECIMAL(5,2),
    soh_min_observe DECIMAL(5,2),
    puissance_max_production DECIMAL(10,2),
    date_maj TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

