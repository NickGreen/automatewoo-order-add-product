 <?php
 /**
  * Plugin Name: AutomateWoo Custom Action Boilerplate
  */


  function register_action( $actions ) {
    include_once '/includes/custom-action.php';
    $actions['order_add_product'] = 'AutomateWoo\Custom_Action';
    return $actions;
  }
  add_filter( 'automatewoo/actions', 'register_action' );
