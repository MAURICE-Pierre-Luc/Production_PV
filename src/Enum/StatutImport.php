<?php

namespace App\Enum;

enum StatutImport: string
{
    case EN_ATTENTE = 'EN_ATTENTE';
    case EN_COURS = 'EN_COURS';
    case TERMINE = 'TERMINE';
    case ERREUR = 'ERREUR';
}
