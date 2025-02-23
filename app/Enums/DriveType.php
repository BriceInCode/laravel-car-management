<?php

namespace App\Enums;

/**
 * Enum pour les types de transmission.
 */
enum DriveType: string
{
    case FWD = 'FWD';  // Transmission avant
    case RWD = 'RWD';  // Propulsion arrière
    case AWD = 'AWD';  // Transmission intégrale
}
