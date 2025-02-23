<?php

namespace App\Enums;

/**
 * Enum pour les types de rôles.
 */
enum RoleType: string
{
    case ADMIN = 'admin';      // Rôle administrateur
    case USER = 'user';        // Rôle utilisateur
    case MODERATOR = 'moderator'; // Rôle modérateur
    case GUEST = 'guest';      // Rôle invité
}
