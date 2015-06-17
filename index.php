<?php
/*
require_once 'vendor/autoload.php';
//require_once 'src/templates/private/_core/Template.php';

use Slim\Slim;

Slim::registerAutoloader();

$app = new Slim( array( 
    'version' => '1.0.0',
    'description' => 'un pouple 2.0',
    'debug' => true,
    'mode' => 'dev',
    'root_path' => dirname( __FILE__ ),
    'templates_path' => dirname( __FILE__ ). '/src/templates/private',
    'routes_path' => __FILE__ . '/src/private/route/',
    'views_path' => __FILE__ . '/src/private/route/',
    'logs_path' => __FILE__ . '/src/private/route/',
    'libs_path' => __FILE__ . '/src/lib/',
    //'view' => new \Slim\Views\Twig()
    )
);
$app->setName( 'pulpy' );

// debug or not 
if( $app->config( 'debug' ) )
{
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}


//require_once 'autoload.php';
require_once 'src/routes/private/_core/Route.php';
require_once 'src/routes/private/_core/Route.php';

$app->run();
 */

require_once 'Pulpy.php';
$pulpy = new \Pulpy\Pulpy();
//print_r( $pulpy );
/*


// verifier app

// lire la conf

// init Slim

// create log 

// create route

// create db

// create template
 */
