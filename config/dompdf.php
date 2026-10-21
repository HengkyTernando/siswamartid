<?php

return [
    // DirectAdmin keeps the Laravel app and the web root in sibling directories.
    // Locally, Laravel serves the public directory inside the project.
    'public_path' => is_dir(base_path('../public_html'))
        ? base_path('../public_html')
        : base_path('public'),
];
