<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit7987d2c5ab4f9b68c6cc92c3fbe035aa
{
    public static $files = array (
        '0e6d7bf4a5811bfa5cf40c5ccd6fae6a' => __DIR__ . '/..' . '/symfony/polyfill-mbstring/bootstrap.php',
    );

    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Symfony\\Polyfill\\Mbstring\\' => 26,
        ),
        'P' => 
        array (
            'PHPMailer\\PHPMailer\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Symfony\\Polyfill\\Mbstring\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-mbstring',
        ),
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
    );

    public static $prefixesPsr0 = array (
        'S' => 
        array (
            'Smalot\\PdfParser\\' => 
            array (
                0 => __DIR__ . '/..' . '/smalot/pdfparser/src',
            ),
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit7987d2c5ab4f9b68c6cc92c3fbe035aa::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit7987d2c5ab4f9b68c6cc92c3fbe035aa::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit7987d2c5ab4f9b68c6cc92c3fbe035aa::$prefixesPsr0;
            $loader->classMap = ComposerStaticInit7987d2c5ab4f9b68c6cc92c3fbe035aa::$classMap;

        }, null, ClassLoader::class);
    }
}