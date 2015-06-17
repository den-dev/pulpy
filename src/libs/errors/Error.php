<?php
namespace Pulpy\Lib\Error;

// config 100
define( 'PLP_CONFIG_ERROR', 100 );

define( 'PLP_CONFIG_DB_ERROR', 101 );
define( 'PLP_CONFIG_DB_INFOS_ERROR', 102 );
define( 'PLP_CONFIG_DB_DRIVER_ERROR', 103 );

define( 'PLP_CONFIG_AUTH_ERROR', 110 );
define( 'PLP_CONFIG_AUTH_CREATE_USER_ERROR', 111 );
// db
define( 'PLP_DB_ERROR', 200 );
define( 'PLP_DB_NOT_FOUND_ERROR', 201 );
// auth
define( 'PLP_AUTH_ERROR', 300 );
define( 'PLP_AUTH_CREATE_USER_ERROR', 301 );
define( 'PLP_AUTH_NOT_AUTHORIZED_ERROR', 302 );
define( 'PLP_AUTH_WEB_NOT_AUTHORIZED_ERROR', 303 );
define( 'PLP_AUTH_API_NOT_AUTHORIZED_ERROR', 304 );
// user
define( 'PLP_USER_ERROR', 400 ); 



class Error extends \Exception
{
    private $_debug_infos;


    public function __construct( $code, $fatal = false, $debug_infos = false )
    {
	parent::__construct( '#FATAL: ' . $this->get_message( $code ), $code, null );

	// debug 
	$this->_debug_infos = $debug_infos;

	// log
	$this->to_log();
	
	// kill
	if( $fatal )
	{
	    if( $debug_infos )
	    {
		exit( $this->to_debug() );
	    }
	    else
	    {
		exit( $this->get_message( $code ) );
	    }
	}
    }

    public function get_message( $code )
    {
	$message = false;

	switch( $code )
	{
	    // CONFIG
	case PLP_CONFIG_ERROR:
	    $message = 'Erreur configuration de Pulpy';
	    break;
	case PLP_CONFIG_DB_ERROR:
	    $message = 'Erreur configuration de la db';
	    break;
	case PLP_CONFIG_DB_INFOS_ERROR:
	    $message = "Erreur configuration de la db, manque d'informations";
	    break;
	case PLP_CONFIG_DB_DRIVER_ERROR:
	    $message = 'Erreur configuration de la db, driver non supporter';
	    break;
	case PLP_CONFIG_AUTH_ERROR:
	    $message = "Erreur configuration de l'authentication";
	    break;
	case PLP_CONFIG_AUTH_CREATE_USER_ERROR:
	    $message = "Erreur configuration de l'authentication, creation utilisateur root";
	    break;
	    // DB
	case PLP_DB_ERROR:
	    $message = "Erreur db";
	    break;
	case PLP_CONFIG_DB_DRIVER_ERROR:
	    $message = "Erreur db, la db demander n'existe pas";
	    break;
	    // AUTH
	case PLP_AUTH_NOT_AUTHORIZED_ERROR:
	    $message = "Erreur authentication, utilisateur non logger";
	    break;
	case PLP_AUTH_WEB_NOT_AUTHORIZED_ERROR;
	    $message = "Erreur authentication, utilisateur non logger";
	    break;
	    // USER
	case PLP_USER_ERROR:
	    $message = 'Pulpy Erreur user';
	    break;
	}

	return $message;
    }

    public function to_log()
    {
	return  $this->message . " [ " . $this->file . " " . $this->line . " ] ";
    }

    public function to_debug()
    {
	return  $this->message . " [ " . $this->file . " " . $this->line . " ] " . print_r( $this->_debug_infos, true );
    }

}
