<?php

namespace App\Enums;

/**
 * Enum pour les types de permissions.
 */
enum PermissionType: string
{
    // Permissions liées aux voitures
    case CREATE_CAR = 'create_car';     // Permission de créer une voiture
    case EDIT_CAR = 'edit_car';         // Permission de modifier une voiture
    case DELETE_CAR = 'delete_car';     // Permission de supprimer une voiture
    case VIEW_CAR = 'view_car';         // Permission de voir une voiture
    case LIST_CARS = 'list_cars';       // Permission de lister les voitures
    case ASSIGN_CAR = 'assign_car';     // Permission d'assigner une voiture à un utilisateur

    // Permissions liées aux utilisateurs
    case CREATE_USER = 'create_user';   // Permission de créer un utilisateur
    case EDIT_USER = 'edit_user';       // Permission de modifier un utilisateur
    case DELETE_USER = 'delete_user';   // Permission de supprimer un utilisateur
    case VIEW_USER = 'view_user';       // Permission de voir un utilisateur
    case LIST_USERS = 'list_users';     // Permission de lister les utilisateurs
    case ACTIVATE_USER = 'activate_user'; // Permission d'activer un utilisateur
    case DEACTIVATE_USER = 'deactivate_user'; // Permission de désactiver un utilisateur

    // Permissions liées aux rôles
    case CREATE_ROLE = 'create_role';   // Permission de créer un rôle
    case EDIT_ROLE = 'edit_role';       // Permission de modifier un rôle
    case DELETE_ROLE = 'delete_role';   // Permission de supprimer un rôle
    case VIEW_ROLE = 'view_role';       // Permission de voir un rôle
    case LIST_ROLES = 'list_roles';     // Permission de lister les rôles
    case ASSIGN_ROLE = 'assign_role';   // Permission d'assigner un rôle à un utilisateur

    // Permissions liées aux permissions
    case CREATE_PERMISSION = 'create_permission'; // Permission de créer une permission
    case EDIT_PERMISSION = 'edit_permission';     // Permission de modifier une permission
    case DELETE_PERMISSION = 'delete_permission'; // Permission de supprimer une permission
    case VIEW_PERMISSION = 'view_permission';     // Permission de voir une permission
    case LIST_PERMISSIONS = 'list_permissions';   // Permission de lister les permissions
    case ASSIGN_PERMISSION = 'assign_permission'; // Permission d'assigner une permission à un rôle ou utilisateur

    // Permissions liées aux messages
    case SEND_MESSAGE = 'send_message'; // Permission d'envoyer un message
    case READ_MESSAGE = 'read_message'; // Permission de lire un message
    case DELETE_MESSAGE = 'delete_message'; // Permission de supprimer un message
    case LIST_MESSAGES = 'list_messages'; // Permission de lister les messages

    // Permissions liées aux logs d'activité
    case VIEW_ACTIVITY_LOG = 'view_activity_log'; // Permission de voir les logs d'activité
    case EXPORT_ACTIVITY_LOG = 'export_activity_log'; // Permission d'exporter les logs d'activité

    // Permissions générales
    case ACCESS_DASHBOARD = 'access_dashboard'; // Permission d'accéder au tableau de bord
    case MANAGE_SETTINGS = 'manage_settings';   // Permission de gérer les paramètres du système

    // Permissions liées à la gestion du profil
    case EDIT_PROFILE = 'edit_profile';         // Permission de modifier les informations du profil
    case CHANGE_PASSWORD = 'change_password';   // Permission de changer le mot de passe
    case UPLOAD_PROFILE_IMAGE = 'upload_profile_image'; // Permission de télécharger une image de profil
    case DELETE_PROFILE_IMAGE = 'delete_profile_image'; // Permission de supprimer l'image de profil
    case VIEW_PROFILE = 'view_profile';         // Permission de voir le profil
}
