<?php
namespace Pulpy\Lib\Db;


class Db
{
    private $_db;


    public function __construct( $configs )
    {
	$this->_set_dbs( $configs );
    }

    public function get_db( $name )
    {
	$db = false;

	if( array_key_exists( $name, $this->_dbs ) )
	{
	    $db = $this->_dbs[$name];
	}
	else
	{
	    new \Pulpy\Lib\Error\Error( PLP_DB_NOT_FOUND_ERROR, true, $name );
	}

	return $db;
    }

    private function _set_dbs( $configs )
    {
	foreach( $configs as $name => $config )
	{
	    // good method for good client
	    switch( $config['client'] )
	    {
	    case 'redbean':
		$db = $this->_set_redbean_db( $config );
		break;
	    case 'eloquent':
		$db = $this->_set_eloquent_db( $config );
		break;
	    }

	    // add 
	    if( $db )
	    {
		$this->_dbs[$name] = $db; // TODO unique
	    }
	}
    }

    private function _set_redbean_db( $config )
    {
	$db = false;
	$authoriezd_drivers = array( 'mysql' );

	// check
	if( array_key_exists( 'driver', $config ) && in_array( $config['driver'], $authoriezd_drivers ) )
	{
	    // check all infos 
	    if( array_key_exists( 'host', $config ) && array_key_exists( 'database', $config ) && array_key_exists( 'username', $config ) && array_key_exists( 'password', $config ) )
	    {
		$driver = $config['driver'];
		$host = $config['host'];
		$dbname = $config['database'];
		$user = $config['username'];
		$password = $config['password'];
		$charset = 'utf8';

		// connect
		$db = \RedBeanPHP\R::setup( "${driver}:host=${host};dbname=${dbname}",$user, $password );
	    }
	    else
	    {
		new \Pulpy\Lib\Error\Error( PLP_CONFIG_DB_INFOS_ERROR, true, $config );
	    }
	}
	else
	{
	    new \Pulpy\Lib\Error\Error( PLP_CONFIG_DB_DRIVER_ERROR, true );
	}

	return $db;
    }

    private function _set_eloquent_db( $config )
    {
	$db = false;
	$authoriezd_drivers = array( 'mysql' );

	// check
	if( array_key_exists( 'driver', $config ) && in_array( $config['driver'], $authoriezd_drivers ) )
	{
	    // check all infos 
	    if( array_key_exists( 'host', $config ) && array_key_exists( 'database', $config ) && array_key_exists( 'username', $config ) && array_key_exists( 'password', $config )
		&& array_key_exists( 'charset', $config ) && array_key_exists( 'collation', $config ) )
	    {
		$db = new \Illuminate\Database\Capsule\Manager();

		// connect
		$db->addConnection( $config );
	    }
	    else
	    {
		new \Pulpy\Lib\Error\Error( PLP_CONFIG_DB_INFOS_ERROR, true, $config );
	    }
	}
	else
	{
	    new \Pulpy\Lib\Error\Error( PLP_CONFIG_DB_DRIVER_ERROR, true );
	}

	return $db;
    }
}
