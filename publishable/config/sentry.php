<?php
// Release sous la forme {$sha}@{$branch}
// Le cache est géré par artisan config:cache
$branch = trim(exec("cat ". base_path('.git') ."/HEAD | cut -d '/' -f 3"));
$sha = substr(trim(exec("cat ". base_path('.git') ."/refs/heads/{$branch}")),0,7);


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
