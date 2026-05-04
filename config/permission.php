<?php

// config/permission.php
// Publié via : php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"

return [

    'models' => [
        /*
         * Spatie utilisera ces modèles par défaut.
         * Vous pouvez les remplacer par vos propres modèles si besoin.
         */
        'permission' => Spatie\Permission\Models\Permission::class,
        'role'       => Spatie\Permission\Models\Role::class,
    ],

    'table_names' => [
        'roles'                 => 'roles',
        'permissions'           => 'permissions',
        'model_has_permissions' => 'model_has_permissions',
        'model_has_roles'       => 'model_has_roles',
        'role_has_permissions'  => 'role_has_permissions',
    ],

    'column_names' => [
        /*
         * Colonne clé étrangère vers votre modèle User dans les tables pivot.
         */
        'role_pivot_key'       => null, // 'role_id'
        'permission_pivot_key' => null, // 'permission_id'
        'model_morph_key'      => 'model_id',
        'team_foreign_key'     => 'team_id',
    ],

    /*
     * Activer le support multi-équipe (désactivé pour easyShop).
     */
    'teams' => false,

    'cache' => [

        /*
         * By default all permissions are cached for 24 hours to speed up performance.
         * When permissions or roles are updated the cache is flushed automatically.
         */

        'expiration_time' => \DateInterval::createFromDateString('24 hours'),

        /*
         * The cache key used to store all permissions.
         */

        'key' => 'spatie.permission.cache',

        /*
         * You may optionally indicate a specific cache driver to use for permission and
         * role caching using any of the `store` drivers listed in the cache.php config
         * file. Using 'default' here means to use the `default` set in cache.php.
         */

        'store' => 'default',
    ],
    
];
