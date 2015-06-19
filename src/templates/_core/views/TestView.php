<?php
namespace Pulpy\View;


class TestView extends \Slim\View
{
    public function render( $template, $data = null )
    {
	return "test";
    }
}
?>
