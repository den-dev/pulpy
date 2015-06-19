<?php
namespace Pulpy\Lib\Error;


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
	    // TEMPLATE
	case PLP_TEMPLATE_CHANGE_VIEW_ERROR:
	    $message = 'Pulpy template Not found';
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
