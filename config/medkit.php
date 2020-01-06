<?php

return [
    'redirect_if_not_admin'=> env('REDIRECT_IF_NOT_ADMIN', '/'),
    'dev_email' => 'dev@mediactive.fr',
    'gdpr' => [
    	'users' => [
    		'name',
    		'first_name',
    		'email',
    		'login'
    	]
    ],
    'clear_directory' => [
        'storage/logs' => 30
    ],
    'path' => [
        'stubs' => resource_path('mediactive-digital/medkit/stubs/')
    ]
];
