<?php
namespace To51\AW_Action;

class TO51_AW_Order_Add_Product {
	public static function init() {
		add_filter( 'automatewoo/actions', array( __CLASS__, 'register_action' ) );
	}

	function register_action( $actions ) {
		require_once __DIR__ . '/includes/class-action-order-add-free-product.php';

		$actions['to51_add_free_product'] = Action_Order_Add_Free_Product::class;
		return $actions;
	}
}

TO51_AW_Order_Add_Product::init();
