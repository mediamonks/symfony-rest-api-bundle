<?php

use Symfony\Component\Yaml\Parser;
use Symfony\Component\HttpKernel\Kernel;

require_once __DIR__.'/../vendor/autoload.php';

$path = __DIR__.'/../vendor/symfony/symfony/src/Symfony/Component/Yaml/Parser.php';

if (function_exists('opcache_is_script_cached')) {
    var_dump(opcache_is_script_cached ($path));
    var_dump(opcache_invalidate($path, true));
    var_dump(opcache_is_script_cached ($path));
    var_dump(opcache_compile_file($path));
    var_dump(opcache_reset());
}

echo 'yaml parser:'.PHP_EOL;
$yamlParser = new Parser();
print_r(get_class_methods($yamlParser));

echo 'version: '.Kernel::VERSION.PHP_EOL;

echo file_get_contents($path);
