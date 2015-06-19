<?php
namespace Pulpy\View;


class HtmlView extends \Slim\View
{
    public function render( $template, $data = null )
    {
	return "<h1>Default View</h1>";
    }
}
?>
