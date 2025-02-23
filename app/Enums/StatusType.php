<?php

namespace App\Enums;

enum StatusType: string
{
    case Available = 'available'; // Le véhicule est disponible à la vente
    case Sold = 'sold'; // Le véhicule a été vendu
    case Pending = 'pending'; // Le véhicule est en attente de confirmation ou de traitement
}
