<?php
// get slim
$app = \Pulpy\Pulpy::getInstance();

// add private api routes
$app->group('/api', function () use ($app) {

   // add route
   $app->get( '/test', function() use ( $app ) {
      $app->change_view( 'json' );
      $app->render( 200, array( 'name' => 'moi' ) );
   });
});
?>
