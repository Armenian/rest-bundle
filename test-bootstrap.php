<?php

declare(strict_types=1);

if (!empty($_ENV['BOOTSTRAP_CLEAR_CACHE_ENV'])) {
    passthru(sprintf(
        'APP_ENV=%s php "%s/tests/bin/console.php" cache:clear --no-warmup',
        $_ENV['BOOTSTRAP_CLEAR_CACHE_ENV'],
        __DIR__
    ));
}

require 'bootstrap.php';
