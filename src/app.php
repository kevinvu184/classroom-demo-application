<?php

$app = new Slim\App([
    'settings' => [
        'displayErrorDetails' => true,
    ],
]);

// Register Twig
$container['view'] = function ($container) {
    return new Slim\Views\Twig(__DIR__ . '/../templates');
};

return $app;