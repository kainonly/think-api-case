<?php

use think\facade\Env;

return [
    'allow_origin' => explode(',', Env::get('cors.system')),
    'allow_credentials' => true,
    'allow_methods' => ['POST', 'OPTIONS'],
    'expose_headers' => [],
    'allow_headers' => ['CONTENT-TYPE', 'X-REQUESTED-WITH'],
    'max_age' => 31536000,
];