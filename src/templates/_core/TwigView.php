<?php
namespace Pulpy\View;


class TwigView extends \Slim\Views\Twig
{
    public function __construct( $templates_path, $debug )
    {
	parent::__construct();

	$this->setTemplatesDirectory( $templates_path );
	$this->parserOptions = array( 'debug' => $debug );
	$this->parserExtensions = array( new \Slim\Views\TwigExtension() );
    }
}
?>
