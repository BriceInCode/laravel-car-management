<?php

namespace App\Enums;

/**
 * Enum pour les types de carburant.
 */
enum FuelType: string
{
    case Petrol = 'Petrol';      // Essence
    case Diesel = 'Diesel';      // Diesel
    case Hybrid = 'Hybrid';      // Hybride
    case Electric = 'Electric';  // Électrique
}
