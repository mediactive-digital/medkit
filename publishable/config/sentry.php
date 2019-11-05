<?php

// Release sous la forme {$sha}@{$branch}
// Le cache est géré par artisan config:cache

$gitPath = base_path('.git');
$branch = $sha = '';

if (is_dir($gitPath)) {

	$branch = trim(exec("cat ". $gitPath ."/HEAD | cut -d '/' -f 3"));
	$sha = substr(trim(exec("cat ". $gitPath ."/refs/heads/{$branch}")),0,7);
}

return [
    'dsn' => env('SENTRY_LARAVEL_DSN', env('SENTRY_DSN')),
    'send_default_pii'=>true,
    // capture release as git sha
    'release' => "{$sha}@{$branch}",
    'breadcrumbs' => [
        // Capture bindings on SQL queries logged in breadcrumbs
        'sql_bindings' => true,
    ],
];
