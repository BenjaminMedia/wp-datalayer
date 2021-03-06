<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit0e10933730cfda0f4fdebe9eb03122c1
{
    public static $prefixLengthsPsr4 = array (
        'B' => 
        array (
            'BonnierDataLayer\\' => 17,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'BonnierDataLayer\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit0e10933730cfda0f4fdebe9eb03122c1::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit0e10933730cfda0f4fdebe9eb03122c1::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
