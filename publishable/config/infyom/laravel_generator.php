<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Paths
    |--------------------------------------------------------------------------
    |
    */

    'path' => [

        'migration'         => database_path('migrations/'),

        'model'             => app_path('Models/'),

        'datatables'        => app_path('DataTables/'),

        'repository'        => app_path('Repositories/'),

        'routes'            => base_path('routes/back/gestion.php'),

        'api_routes'        => base_path('routes/api.php'),

        'request'           => app_path('Http/Requests/'),

        'api_request'       => app_path('Http/Requests/API/'),

        'controller'        => app_path('Http/Controllers/'),

        'api_controller'    => app_path('Http/Controllers/API/'),

        'repository_test'   => base_path('tests/Repositories/'),

        'api_test'          => base_path('tests/APIs/'),

        'tests'             => base_path('tests/'),

        'views'             => resource_path('views/'),

        'schema_files'      => resource_path('model_schemas/'),

        'templates_dir'     => resource_path('infyom/infyom-generator-templates/'),

        'seeder'            => database_path('seeds/'),

        'database_seeder'   => database_path('seeds/DatabaseSeeder.php'),

        'modelJs'           => resource_path('assets/js/models/'),

        'factory'           => database_path('factories/'),

        'view_provider'     => app_path('Providers/ViewServiceProvider.php'),

        'forms'             => app_path('Forms/'),

        'middlewares'       => app_path('Http/Middleware/'),
		
        'app_provider'     => app_path('Providers/AppServiceProvider.php'),

        'providers'       => app_path('Providers/'),
		
        'policies'       => app_path('Policies/'),
		
        'auth_provider'     => app_path('Providers/AuthServiceProvider.php'),

		
    ],

    /*
    |--------------------------------------------------------------------------
    | Namespaces
    |--------------------------------------------------------------------------
    |
    */

    'namespace' => [

        'model'             => 'App\Models',

        'datatables'        => 'App\DataTables',

        'repository'        => 'App\Repositories',

        'controller'        => 'App\Http\Controllers',

        'api_controller'    => 'App\Http\Controllers\API',

        'request'           => 'App\Http\Requests',

        'api_request'       => 'App\Http\Requests\API',

        'repository_test'   => 'Tests\Repositories',

        'api_test'          => 'Tests\APIs',

        'tests'             => 'Tests',

        'forms'             => 'App\Forms',

        'policies'        => 'App\Policies',
    ],

    /*
    |--------------------------------------------------------------------------
    | Templates
    |--------------------------------------------------------------------------
    |
    */

    'templates'         => 'medkit-theme-malabar',

    /*
    |--------------------------------------------------------------------------
    | Model extend class
    |--------------------------------------------------------------------------
    |
    */

    'model_extend_class' => 'Eloquent',

    /*
    |--------------------------------------------------------------------------
    | API routes prefix & version
    |--------------------------------------------------------------------------
    |
    */

    'api_prefix'  => 'api',

    'api_version' => 'v1',

    /*
    |--------------------------------------------------------------------------
    | Options
    |--------------------------------------------------------------------------
    |
    */

    'options' => [

        'softDelete' => true,

        'userStamps' => true,

        'flashValidationErrors' => false,

        'save_schema_file' => true,

        'tables_searchable_default' => false,

        'repository_pattern' => true,

        'excluded_fields' => ['id'], // Array of columns that doesn't required while creating module
    ],

    /*
    |--------------------------------------------------------------------------
    | Prefixes
    |--------------------------------------------------------------------------
    |
    */

    'prefixes' => [

        'route' => 'back',  // using admin will create route('admin.?.index') type routes

        'path' => '',

        'view' => '',  // using backend will create return view('backend.?.index') type the backend views directory

        'public' => '',
    ],

    /*
    |--------------------------------------------------------------------------
    | Add-Ons
    |--------------------------------------------------------------------------
    |
    */

    'add_on' => [

        'swagger'       => false,

        'tests'         => true,

        'datatables'    => true,

        'menu'          => [

            'enabled'       => true,

            'menu_file'     => 'GenerateMenus.php',
        ],

        'tracks_history'          => [

            'enabled'       => true, // track crud  in history table

            'provider_file'     => 'AppServiceProvider.php',
        ],

        'permissions'          => [
			
			'policies'    => true,  // Create the crud Policy Class
			
            'enabled'       => true, // Create the crud permissions and assign to superAdmin Role
			
            'superadmin_role_id'       => 1, 
        ],
		
        'forms' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Timestamp Fields
    |--------------------------------------------------------------------------
    |
    */

    'timestamps' => [

        'enabled'       => true,

        'created_at'    => 'created_at',

        'updated_at'    => 'updated_at',

        'deleted_at'    => 'deleted_at',
    ],

    'user_stamps' => [

        'enabled'       => true,

        'created_by'    => 'created_by',

        'updated_by'    => 'updated_by',

        'deleted_by'    => 'deleted_by',
    ],

    'gdpr' => [

        'enabled'       => true,

        'last_activity' => 'last_activity',
    ],

    /*
    |--------------------------------------------------------------------------
    | Save model files to `App/Models` when use `--prefix`. see #208
    |--------------------------------------------------------------------------
    |
    */
    'ignore_model_prefix' => false,

    /*
    |--------------------------------------------------------------------------
    | Specify custom doctrine mappings as per your need
    |--------------------------------------------------------------------------
    |
    */
    'from_table' => [

        'doctrine_mappings' => [],
    ],

];
