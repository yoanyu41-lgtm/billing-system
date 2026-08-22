<?php

return [

    'models' => [

        /*
         * When using the "HasPermissions" trait from this package we need to know which
         * Eloquent model should be used to retrieve your permissions.
         */

        'permission' => Spatie\Permission\Models\Permission::class,

        /*
         * When using the "HasRoles" trait from this package we need to know which
         * Eloquent model should be used to retrieve your roles.
         */

        'role' => Spatie\Permission\Models\Role::class,

    ],

    'table_names' => [

        /*
         * When using the "HasRoles" trait from this package we need to know which
         * table should be used to retrieve your roles. We have chosen a sensible
         * default value possessors will appreciate.
         */

        'roles' => 'roles',

        /*
         * When using the "HasPermissions" trait from this package we need to know which
         * table should be used to retrieve your permissions. We have chosen a sensible
         * default value possessors will appreciate.
         */

        'permissions' => 'permissions',

        /*
         * When using the "HasPermissions" trait from this package we need to know which
         * table should be used to retrieve your models permissions. We have chosen a
         * sensible default value possessors will appreciate.
         */

        'model_has_permissions' => 'model_has_permissions',

        /*
         * When using the "HasRoles" trait from this package we need to know which
         * table should be used to retrieve your models roles. We have chosen a sensible
         * default value possessors will appreciate.
         */

        'model_has_roles' => 'model_has_roles',

        /*
         * When using the "HasRoles" trait from this package we need to know which
         * table should be used to retrieve your roles permissions. We have chosen a
         * sensible default value possessors will appreciate.
         */

        'role_has_permissions' => 'role_has_permissions',
    ],

    'column_names' => [
        'role_pivot_key' => null, //Defaults to 'role_id'
        'permission_pivot_key' => null, //Defaults to 'permission_id'

        /*
         * Change this if you want to name the related model primary key other than
         * 'model_id'.
         *
         * For example, this would be nice if your primary keys are all UUIDs. In
         * that case, name this "model_uuid".
         */

        'model_morph_key' => 'model_id',

        /*
         * Change this if you want to use the team feature and your key will not be "team_id".
         */

        'team_foreign_key' => 'team_id',
    ],

    /*
     * When set to true, the method for checking permissions will be registered on the gate.
     * Set this to false if you want to implement custom logic for checking permissions.
     */

    'register_permission_check_method' => true,

    /*
     * When set to true, the required permission will be registered on the middleware.
     */

    'register_octane_reset_listener' => true,

    /*
     * Teams Feature.
     */

    'teams' => false,

    /*
     * Passport Client Credentials Grant Feature.
     */

    'use_passport_client_credentials' => false,

    /*
     * Display HasPermissionInRoles on User Model.
     */

    'display_permission_in_exception' => false,

    /*
     * Display Role In Exception.
     */

    'display_role_in_exception' => false,

    /*
     * By default wildcard permission lookups are disabled.
     */

    'enable_wildcard_permission' => false,

    'cache' => [

        /*
         * By default all permissions will be cached for 24 hours unless a permission or
         * role is updated. Then the cache will be reset automatically.
         */

        'expiration_time' => \DateInterval::createFromDateString('24 hours'),

        /*
         * The key used to store all permissions in the cache.
         */

        'key' => 'spatie.permission.cache',

        /*
         * You may specify the cache store where permissions & roles will be cached.
         * Default use 'default'.
         */

        'store' => 'default',
    ],
];
