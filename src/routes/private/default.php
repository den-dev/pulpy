<?php 
// /private/
$app->get('/version', array( $app, 'is_authenticated' ), function() use ( $app ) {
 // Login credentials
   /*
    $credentials = array(
        'email'    => 'pulpy',
        'password' => 'pulpy',
    );

    // Authenticate the user
    $auth = $app->get_arg_config( 'auth' );
    $user = $auth::authenticate($credentials, false);
   if( $app->is_authorized() )
   {


    //echo Slim

    //echo $app->getName() . '<br>';
    //echo 'version: ' .$app->config( 'version' ) . '<br>';
    //echo 'description: ' . $app->config( 'description' ) . '<br>';
    //echo 'debug: ' . $app->config( 'debug' ) . '<br>';
    //echo 'mode: '. $app->config( 'mode' ) . '<br>';
    //Show book identified by $id

   // twig
    */
   $app->change_view( 'twig' );
   $app->render( 'private/version.php', array( 
      'nom' => $app->getName(),
      'version' => $app->get_arg_config( 'version' ),
      'description' => $app->get_arg_config( 'description' ),
      'debug' => $app->get_arg_config( 'debug' ),
      'mode' => $app->get_arg_config( 'mode' ) ) 
   );
   /*
   }
   else
   {
        $app->flash('error', 'jkljkljlkjkljk');

        //$app->redirect('/error');

   }
   /*
   // json
   $app->change_view( 'json' );
   $app->render( 200, array( 'name' => 'moi' ) );
    */
});

$app->get( '/test', function() use ( $app ) {

   $app->change_view( 'twig' );
   $log = $app->get_arg_config( 'log.writer' );
   //print_r( $log );

   $log->write( 'debug', 'test logger', 'debug' );
   // $app->response->setStatus( 400 );
   // $app->response->write('Bar');

}); 

$app->get('/hello/:name', function ($name) {
    echo "Hello, $name";
});
?>
