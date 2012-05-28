<?php
namespace webignition\Url;

function autoload( $rootDir ) {
    spl_autoload_register(function( $className ) use ( $rootDir ) {        
        $file = sprintf(
            '%s/%s.php',
            $rootDir,
            str_replace( '\\', '/', $className )
        );        
        
        if ( file_exists($file) ) {
            require $file;
        }
    });
}

autoload( '/usr/share/php' );
autoload( __DIR__ . '/../tests');
autoload( __DIR__ . '/../tests/regular');
autoload( __DIR__ . '/../tests/normalisation');
autoload( __DIR__ . '/../src');