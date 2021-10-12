<?php

use AutomateWoo\Action;
use AutomateWoo\Fields;


class Order_Add_Product extends AutomateWoo\Action {


  public $required_data_items = [ 'order' ];


  function init() {
    $this->title       = __( 'Test', 'automatewoo' );
    $this->description = __( 'Test', 'automatewoo' );
    $this->group       = __( 'Order', 'automatewoo' );
  }


  function load_fields() {

    $email = ( new AutomateWoo\Fields\Text() )
    ->set_name( 'email' )
    ->set_title( __( 'Email', 'automatewoo' ) )
    ->set_variable_validation()
    ->set_required();

    $this->add_field( $email );
  }


  function run() {
  }

}
