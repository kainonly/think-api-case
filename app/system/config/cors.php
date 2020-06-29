<?php
declare (strict_types=1);
return [
    'allow_origin' => explode(',', env('cors.system')),
    'allow_credentials' => true,
    'allow_methods' => ['POST', 'OPTIONS'],
    'expose_headers' => [],
    'allow_headers' => ['CONTENT-TYPE', 'X-REQUESTED-WITH'],
    'max_age' => 31536000,
];