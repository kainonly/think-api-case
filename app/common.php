<?php
declare(strict_types=1);

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use think\facade\Env;

if (!function_exists('get_schema_manager')) {
    /**
     * @return AbstractSchemaManager
     * @throws Exception
     */
    function get_schema_manager(): AbstractSchemaManager
    {
        $conn = DriverManager::getConnection([
            'dbname' => Env::get('database.database'),
            'user' => Env::get('database.username'),
            'password' => Env::get('database.password'),
            'host' => Env::get('database.hostname'),
            'port' => Env::get('database.hostport'),
            'charset' => Env::get('database.charset'),
            'driver' => 'mysqli',
        ]);
        return $conn->getSchemaManager();
    }
}

if (!function_exists('get_table_name')) {
    /**
     * @param string $name
     * @return string
     */
    function get_table_name(string $name): string
    {
        return Env::get('database.prefix') . $name;
    }
}

if (!function_exists('get_client_ip')) {
    /**
     * Get client ip.
     *
     * @return string
     */
    function get_client_ip(): string
    {
        if (!empty($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        } else {
            // for php-cli(phpunit etc.)
            $ip = defined('PHPUNIT_RUNNING') ? '127.0.0.1' : gethostbyname(gethostname());
        }

        return filter_var($ip, FILTER_VALIDATE_IP) ?: '127.0.0.1';
    }
}