<?php
/**
 * Test bootstrap.
 *
 * Works both with Composer (vendor/autoload.php is used when present) and with a
 * standalone PHPUnit PHAR (fallback PSR-4 autoloader for lib/ + tests/).
 */
if (is_file(dirname(__DIR__) . '/vendor/autoload.php')) {
    require dirname(__DIR__) . '/vendor/autoload.php';
} else {
    spl_autoload_register(function ($class) {
        $map = array(
            'Dwoo\\'        => dirname(__DIR__) . '/lib/Dwoo/',
            'Dwoo\\Tests\\' => __DIR__ . '/',
        );
        foreach ($map as $prefix => $base) {
            if (strncmp($class, $prefix, strlen($prefix)) === 0) {
                $file = $base . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
                if (is_file($file)) {
                    require $file;
                }
            }
        }
    });
}

// Start from a clean compile/cache state. Dwoo keeps an in-process static
// "already compiled" cache, so deleting compiled files mid-run can make later
// tests believe a template is compiled while the file is gone.
foreach (array(__DIR__ . '/temp/cache', __DIR__ . '/temp/compiled') as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
        continue;
    }
    foreach (glob($dir . '/*') as $file) {
        if (is_file($file)) {
            @unlink($file);
        }
    }
}
