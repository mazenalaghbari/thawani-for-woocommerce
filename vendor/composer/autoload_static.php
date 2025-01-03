<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitb637f60363ff2e38139e0d20e1a4920b
{
    public static $prefixLengthsPsr4 = array (
        'T' => 
        array (
            'Thawani\\' => 8,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Thawani\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitb637f60363ff2e38139e0d20e1a4920b::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitb637f60363ff2e38139e0d20e1a4920b::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitb637f60363ff2e38139e0d20e1a4920b::$classMap;

        }, null, ClassLoader::class);
    }
}
