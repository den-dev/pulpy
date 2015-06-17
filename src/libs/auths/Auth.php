<?php 
namespace Pulpy\Lib\Auth;

class Auth extends \Cartalyst\Sentry\Facades\Native\Sentry
{
    public function __construct( $db, $username, $password )
    {
	/*
	 * TODO cree user root mais pas le logger 
	 */
	//class_alias('Cartalyst\Sentry\Facades\Native\Sentry', 'Sentry');
	$db->bootEloquent();
	$this->create_group( 'administrator', array( 'admin' => 1, 'users' => 1 ) );
	$this->create_user( $username, $password, 'administrator' );
    }

    public function create_user( $username, $password, $group_name )
    {
	$ok = false;

	try
	{
	    // get token
	    $token = $this->_create_token( $username );

	    // create user 
	    $user = self::createUser( array(
		'email'    => $username,
		'password' => $password,
		'token' => $token,
		'activated' => true,
	    ));

	    // get group
	    $group = Sentry::findGroupByName( $group_name );

	    // add user to group
	    $user->addGroup( $group );
	}
	catch( \Cartalyst\Sentry\Users\UserExistsException $e )
	{
	     // nothing to do 
	}
	catch( \Exception $e )
	{
	    new \Pulpy\Lib\Error\Error( PLP_CONFIG_AUTH_CREATE_USER_ERROR, true, $e );
	}


	return $ok;
    }

    public function create_group( $name, $permissions )
    {
	try
	{
	    // Create the group
	    $group = self::createGroup( array(
		'name'        => $name,
		'permissions' => $permissions
	    ) );
	}
	catch( \Cartalyst\Sentry\Groups\GroupExistsException $e)
	{
	    // nothing to do 
	}
	catch( \Cartalyst\Sentry\Groups\NameRequiredException $e)
	{
	    new \Pulpy\Lib\Error\Error( PLP_AUTH_CREATE_USER_ERROR, true, $e );
	}
    }

    private function _create_root()
    {
    }

    private function register_user( $username, $password )
    {
	$ok = false;

	try
	{
	    $token = $this->_create_token( $username );
	    // create user
	    self::register( array(
		'email'    => $username,
		'password' => $password,
		'token' => $token,
	    ), true );
	    $ok = true;

	}
	catch( \Cartalyst\Sentry\Users\UserExistsException $e )
	{
	    // ok 
	}
	catch( Exception $e )
	{
	    new \Pulpy\Lib\Error\Error( PLP_CONFIG_AUTH_CREATE_USER_ERROR, true, $e );
	}


	return $ok;
    }

    private function _create_token( $username )
    {
	return md5( uniqid( $username, true ) );

    }

    public function is_authorized()
    {
	$ok = false;

	if( self::check() )
	{
	    $ok = true;   
	}
	else
	{
	    new \Pulpy\Lib\Error\Error( PLP_AUTH_NOT_AUTHORIZED_ERROR );
	}

	return $ok;
    }
}
/*
https://cartalyst.com/manual/sentry/2.1#permissions

Groups Permissions
0 : Deny
1 : Allow
Users Permissions
-1 : Deny
 1 : Allow
 0 : Inherit
 */
