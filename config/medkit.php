<?php

return [
    'redirect_if_not_admin'=> env('REDIRECT_IF_NOT_ADMIN', '/'),
    'dev_email' => 'dev@mediactive.fr',
    'gdpr' => [
    	'users' => [
    		'name',
    		'firstname',
    		'email',
    		'login'
    	]
    ]
];
