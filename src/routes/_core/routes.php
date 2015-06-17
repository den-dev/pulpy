<?php
// get sli
$app = \Pulpy\Pulpy::getInstance();

// add pulpy routes
$app->group('/pulpy', function () use ($app) {

    // add private routes
    $app->group('/private', function () use ($app) {

	foreach( $app->get_files_list( $app->get_arg_config( 'routes_path' ) . 'private' ) as $file )
	{
	    include_once $file;
	}   
    });

    // add private routes
    $app->group('/public', function () use ($app) {

	foreach( $app->get_files_list( $app->get_arg_config( 'routes_path' ) . 'private' ) as $file )
	{
	    include_once $file;
	}   
    });
});
?>
