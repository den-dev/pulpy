<?php
namespace Pulpy;
require_once 'src/libs/errors/errors_define.php';
require_once 'vendor/autoload.php';
require_once 'autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
 
/**
 * 
 *
 */
class Pulpy extends \Slim\Slim
{
    public function __construct()
    {
	$this->_init_slim();
	$this->_set_logger();
	$this->_set_dbs();
	$this->_set_auth();
	$this->_set_route();
	$this->_define_views();

	$this->run();
    }

    /**
     * Récupere les options de configuration.
     *
     * Récupere une option d'apres son nom au sein des option definit dans slim.
     *
     * @param string $name le nom de l'option à récuperer.
     *
     * @return mixed le valeur de l'option demander.
     */
    public function get_arg_config( $name )
    {
	return $this->__get( $name );
    }

    /**
     * Fournit la list des fichiers php présent dans un répertoire.
     *
     * Liste le répertoire est renvoi la liste de tout ses fichiers.
     *
     * @param string $directory le nom avec chemin ( absolue | relatif ) du répertoire.
     *
     * @return array la liste des fichiers php trouver. tableau vide si rien trouver. 
     */
    public function get_files_list( $directory )
    {
	$directory = realpath( $directory );

	$objects = new \RecursiveIteratorIterator( new \RecursiveDirectoryIterator( $directory ), \RecursiveIteratorIterator::SELF_FIRST );
	foreach( $objects as $name => $object )
	{
	    if( is_file( $name ) )
	    {
		$file_infos = pathinfo( $name );
		if( $file_infos['extension'] == 'php' )
		{
		    $files[] = $name;
		}
	    }

	}

	return $files;
    }

    /**
     * Permet de changer la class de view.
     *
     * Applique la vue demander comme vue par defaut pour le render.
     * 
     */
    public function change_view( $view_type )
    {
	$ok = false;
	$view_name = false;

	// get view info
	if( array_key_exists( $view_type, $this->_views_available ) )
	{
	    $view_infos = $this->_views_available[$view_type];

	    // set view
	    $full_view_path = $this->get_arg_config( 'views_path' ) . $view_infos['path'] . $view_infos['file_name'];
	    if( file_exists( $full_view_path ) )
	    {
		include_once $full_view_path;

		// convention: name file_name == class name 
		$class_name = "\Pulpy\\View\\" . str_replace( '.php', '', $view_infos['file_name'] );
		switch( $view_type )
		{
		case 'twig':
		    $this->container->set( 'view', new $class_name( $this->get_arg_config( 'templates.path' ), $this->get_arg_config( 'debug' ) ) );
		    break;
		case 'json':
		    $this->container->set( 'view', new $class_name() );
		    $this->add( new \JsonApiMiddleware() );
		    break;
		default:
		    $this->container->set( 'view', new $class_name() );
		}
		$ok = true;
	    }
	    else
	    {
		$this->log_error( PLP_TEMPLATE_CHANGE_VIEW_ERROR, false );
	    }
	}
	else
	{
	    $this->log_error( PLP_TEMPLATE_CHANGE_VIEW_ERROR, false );
	}


	return $ok;
    }

    public function write_log( $log_name, $message, $level )
    {
	$logs = $this->get_arg_config( 'log.writer' );
	$logs->write( $log_name, $message, $level );
    }

    public function log_error( $code, $fatal = false, $debug_infos = false )
    {
	// write custom exception
	$error = new \Pulpy\Lib\Error\Error( $code, $fatal, $debug_infos );
	return $error->to_debug();
    }

    public function get_db( $name )
    {
	$db = false;

	$manager = $this->get_arg_config( 'db' );
	if( is_object( $manager ) )
	{
	    $db = $manager->get_db( $name );
	}

	return $db;
    }

    public function is_authenticated( $error = false, $fatal = false )
    {
	$ok = false;

	//
	$auth = $this->get_arg_config( 'auth' );
	if( ! $auth->is_authorized() )
	{
	  //
	  if( $error )
	  {
	    // log error 
	    $e = new \Pulpy\Lib\Error\Error( PLP_AUTH_WEB_NOT_AUTHORIZED_ERROR );

	    // get base url 
	    $url = $this->get_arg_config( 'private_web_url' );

	    // redirect 
	    if( $fatal )
	    {
	      $this->redirect( $url . 'error', 301 );
	    }
	    else
	    {
	      $this->redirect( $url . 'login' );
	    }
	  }
	}
	else
	{
	  $ok = true;
	}


	return $ok;
    }

    /*
    public function is_authorized() // check specifiq right
    {
      $auth = $this->get_arg_config( 'auth' );
      return $auth->is_authorized();
    }
     */

    // - 
    private function _init_slim()
    {
      parent::__construct();
      \Slim\Slim::registerAutoloader();

      $this->setName( 'pulpy' );
      $this->container->set( 'version', '1.0.0' );
      $this->container->set( 'description', 'un pouple 2.0' );
      $this->container->set( 'debug', true );
      $this->container->set( 'mode', 'dev' );
      // path
      $this->container->set( 'root_path', dirname( __FILE__ ) );
      $this->container->set( 'routes_path', dirname( __FILE__ ) . '/src/routes/' );
      $this->container->set( 'views_path', dirname( __FILE__ ) . '/src/templates/_core/views/' );
      $this->container->set( 'templates.path', dirname( __FILE__ ) . '/src/templates/' );
      $this->container->set( 'logs_path', dirname( __FILE__ ) . '/logs/' );
      // assets
      $this->container->set( 'css_path', dirname( __FILE__ ) . '/src/assets/css/stylesheets/' );
      $this->container->set( 'js_path', dirname( __FILE__ ) . '/src/assets/js/' );
      $this->container->set( 'img_path', dirname( __FILE__ ) . '/src/assets/img/' );
      // url
      $this->container->set( 'root_url',  "http://$_SERVER[HTTP_HOST]" . '/pulpy/pulpy' );
      $this->container->set( 'private_web_url',  $this->get_arg_config( 'root_url' ) . '/private/web/' );
      $this->container->set( 'private_api_url',  $this->get_arg_config( 'root_url' ) . '/private/api/' );
      $this->container->set( 'public_web_url',  $this->get_arg_config( 'root_url' ) . '/public/web/' );
      $this->container->set( 'public_api_url',  $this->get_arg_config( 'root_url' ) . '/public/api/' );
      // auth
      $this->container->set( 'username', 'pulpy' );
      $this->container->set( 'password', 'pulpy' );

      // debug or not 
      if( $this->get_arg_config( 'debug' ) )
      {
	$this->log->setLevel( \Slim\Log::DEBUG );

	error_reporting(E_ALL);
	ini_set('display_errors', '1');
      }
    }

    private function _set_route( $route_file_name = 'routes' )
    {
      //  select routes
      $route_path =  $this->get_arg_config( 'routes_path' ) . '_core/' . $route_file_name . '.php';

      // add 
      if( file_exists( $route_path ) )
      {
	include_once $route_path;
      }
    }

    private function _define_views()
    {
      $path_base = $this->get_arg_config( 'view_path' );

      // define all possible view 
      $this->_views_available = array(
	'default' => array( 
	  'file_name' => 'TestView.php', 
	  'path' => $path_base 
	),
	'html' => array( 
	  'file_name' => 'HtmlView.php',
	  'path' => $path_base 
	),
	'smarty' => array( 
	  'file_name' => 'SmartyView.php',
	  'path' => $path_base
	),
	'twig' => array( 
	  'file_name' => 'TwigView.php',
	  'path' => $path_base
	),
	'json' => array( 
	  'file_name' => 'JsonView.php',
	  'path' => $path_base
	),

      );
      // set default view

      $this->change_view( 'default' );


      return count( $this->_views_available );
    }

    private function _set_logger()
    {
      $logs_name = array( 'pulpy', 'route', 'view', 'error', 'debug' );
      $logger = new \Pulpy\Lib\Logger\Logger( $logs_name, $this->get_arg_config( 'logs_path' ) );

      $this->container->set( 'log.enabled', true );
      $this->container->set( 'log.writer', $logger );
    }

    private function _set_dbs()
    {
      // pulpy with redbean
      $pulpy = array( 
	'client'    => 'redbean',
	'driver'    => 'mysql',
	'host'      => 'localhost',
	'database'  => 'pulpy',
	'username'  => 'pulpy',
	'password'  => 'pulpy',
	'charset'   => 'utf8',
	'collation' => 'utf8_unicode_ci',
      );

      // auth with sentry and eloquent
      $auth = array( 
	'client'    => 'eloquent',
	'driver'    => 'mysql',
	'host'      => 'localhost',
	'database'  => 'pulpy',
	'username'  => 'pulpy',
	'password'  => 'pulpy',
	'charset'   => 'utf8',
	'collation' => 'utf8_unicode_ci',
      );

      // test with eloquent
      $test = array( 
	'client'    => 'eloquent',
	'driver'    => 'mysql',
	'host'      => 'localhost',
	'database'  => 'database',
	'username'  => 'root',
	'password'  => '',
	'charset'   => 'utf8',
	'collation' => 'utf8_unicode_ci',
      );


      $configs = array( 'pulpy' => $pulpy, 'auth' => $auth, 'test' => $test );

      $this->container->set( 'db', new \Pulpy\Lib\Db\Db( $configs ) );
    }

    private function _set_auth()
    {
      // sentry db 
      $db = $this->get_db( 'auth' );

      // admin 
      $username = $this->get_arg_config( 'username' );
      $password = $this->get_arg_config( 'password' );

      //
      $this->container->set( 'auth', new \Pulpy\Lib\Auth\Auth( $db, $username, $password ) );
    }

    // static
    public static function get_instance()
    {
      return \Slim\Slim::getInstance();
    }
}

?>
