<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';

cors();
require_method('POST');

session_unset();
session_destroy();
json_response(['ok' => true]);


