<?php
// get slim
$app = \Pulpy\Pulpy::getInstance();

// add private web routes
$app->group('/web', function () use ($app) {

   $app->get( '/test', function() use ( $app ) {
      $app->change_view( 'twig' );

      $app->render( 'private/version.php', array( 
      'nom' => $app->getName(),
      'version' => $app->get_arg_config( 'version' ),
      'description' => $app->get_arg_config( 'description' ),
      'debug' => $app->get_arg_config( 'debug' ),
      'mode' => $app->get_arg_config( 'mode' ) ) 
   );

   });

   $app->get( '/login', function() use ( $app ) {
      echo " login" ;
   });

   $app->get( '/logout', function() use ( $app ) {
      echo " logout" ;
   });

   $app->get( '/subscribe', function() use ( $app ) {
      echo " subscribe" ;
   });

});
?>
