<?php

return [
    'paths' => [
        base_path('custom_views'),
    ],
    'compiled' => env(
        'VIEW_COMPILED_PATH',
        realpath(storage_path('framework/views'))
    ),
];