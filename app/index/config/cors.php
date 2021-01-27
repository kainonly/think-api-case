<?php
declare (strict_types=1);
return [
    'allow_origin' => ['*'],
    'allow_credentials' => false,
    'allow_methods' => ['GET', 'POST', 'OPTIONS'],
    'expose_headers' => [],
    'allow_headers' => ['CONTENT-TYPE', 'X-REQUESTED-WITH'],
    'max_age' => 31536000,
];