<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit617d0c259dd8a0ff0bdd71d93dfe1126
{
    public static $prefixLengthsPsr4 = array (
        'R' => 
        array (
            'Respect\\' => 8,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Respect\\' => 
        array (
            0 => __DIR__ . '/..' . '/respect/rest/library/Respect',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit617d0c259dd8a0ff0bdd71d93dfe1126::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit617d0c259dd8a0ff0bdd71d93dfe1126::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
