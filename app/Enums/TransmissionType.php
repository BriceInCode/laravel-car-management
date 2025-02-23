<?php

namespace App\Enums;

/**
 * Enum pour les types de boîte de vitesses.
 */
enum TransmissionType: string
{
    case Manual = 'Manual';       // Boîte manuelle
    case Automatic = 'Automatic'; // Boîte automatique
    case SemiAutomatic = 'Semi-Automatic'; // Boîte semi-automatique
}
