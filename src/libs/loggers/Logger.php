<?php
namespace Pulpy\Lib\Logger;


class Logger 
{
    private $_log_path;
    private $_loggers;


    public function __construct( $logs_name, $log_path )
    {
	$this->_log_path = $log_path;
	$this->_create_loggers( $logs_name );
    }

    public function write( $log_type, $message, $level )
    { 
	$ok = false;

	$log_type = strtolower( $log_type );
	$level = strtolower( $level );

	// get logger
	if( array_key_exists( $log_type, $this->_loggers ) )
	{
	    $log = $this->_loggers[$log_type];

	    // get method to write
	    if( in_array( $level, array( 'debug', 'info', 'notice', 'warning', 'error', 'critical', 'alert', 'emergency' ) ) )
	    {
		$name_methode_add = 'add' . ucfirst( $level );
		$ok = $log->$name_methode_add( $message );
	    }
	}


	return $ok;
    }

    private function _create_loggers( $logs_name )
    {
	$nb = 0;

	foreach( $logs_name as $log_name )
	{
	    $ok = $this->_create_logger( $log_name );
	    $nb = ( $ok ) ? $nb++: $nb;
	}


	return $nb;
    }

    private function _create_logger( $log_name )
    {
	$ok = false;
	$log_name = strtolower( $log_name );

	$log = new \Monolog\Logger( $log_name );
	if( file_exists( $this->_log_path ) && is_writable( $this->_log_path ) )
	{
	    // general handler
	    $log->pushHandler( new \Monolog\Handler\StreamHandler( $this->_log_path . strtolower( $log_name ) . '.log', \Monolog\Logger::DEBUG ) );

	    // specific handler
	    if( $log_name == 'debug' )
	    {
		$log->pushHandler( new \Monolog\Handler\FirePHPHandler() );
		$log->pushHandler( new \Monolog\Handler\ChromePHPHandler() );
	    }

	    // processor
	   $log->pushProcessor( new \Monolog\Processor\IntrospectionProcessor() ); 

	    // end
	    $this->_loggers[$log_name] = $log;
	    $ok = true;
	}
	else
	{
	    echo " ERROR " . $this->_log_path . " not found or not writable";
	}


	return $ok;
    }

}
