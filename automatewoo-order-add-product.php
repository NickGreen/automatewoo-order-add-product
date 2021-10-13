 <?php
 /**
  * Plugin Name: AutomateWoo Order Add Product Action
  */


  function register_action( $actions ) {

    include_once ( plugin_dir_path( __FILE__ ) . 'includes/AbstractEditItem.php' );
    include_once ( plugin_dir_path( __FILE__ ) . 'includes/Order_Edit_Product_Abstract.php' );
    include_once ( plugin_dir_path( __FILE__ ) . 'includes/Order_Add_Product.php' );

    $actions['order_add_product'] = 'AutomateWoo\Action_Order_Add_Product';

    return $actions;
  }
  add_filter( 'automatewoo/actions', 'register_action' );
