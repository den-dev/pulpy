<?php
/**
 * Just for class loading.
 *
 */

spl_autoload_register(function ($class) {
    $paths = array( 
	'/src/',
	// lib
	'/src/libs/',
	'/src/libs/loggers/',
	'/src/libs/errors/',
	'/src/libs/dbs/',
	'/src/libs/auths/',
	// dbs
	'/src/dbs/',
	'/src/dbs/_core/',
	'/src/dbs/private/',
	'/src/dbs/public/',
	// routes
	// -by pulpy, nothing to do
	// templates
	// -by pulpy, nothing to do
	
    );

    foreach( $paths as $path )
    {
	$parts = explode( '\\', $class );
	$file_to_load = dirname( __FILE__ ) . $path . end( $parts ) . '.php'; 
	if( file_exists( $file_to_load  ) )
	{
	    include_once $file_to_load;
	}
    }
});
?>
