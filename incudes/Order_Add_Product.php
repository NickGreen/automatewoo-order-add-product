<?php

namespace AutomateWoo;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Action to add a chosen product line item to a order with a chosen quantity.
 *
 * @class Action_Order_Add_Product
 * @since 4.4
 */
class Action_Order_Add_Product extends Action_Order_Edit_Product_Abstract {


	/**
	 * Variable products should not be added as a line item to orders, only variations.
	 *
	 * @var bool
	 */
	protected $allow_variable_products = false;


	/**
	 * Flag to define whether the instance of this action requires a name text input field.
	 *
	 * @var bool
	 */
	protected $load_name_field = true;


	/**
	 * Flag to define whether the instance of this action requires a price input field to
	 * be displayed on the action's admin UI.
	 *
	 * @var bool
	 */
	protected $load_cost_field = true;


	/**
	 * Explain to store admin what this action does via a unique title and description.
	 */
	public function load_admin_details() {
		parent::load_admin_details();
		$this->title       = __( 'Add Product', 'automatewoo' );
		$this->description = __( 'Add a product as a new line item on a order. The item will be added using the price set on the product.', 'automatewoo' );
	}


	/**
	 * Add a given product as a line item to a given order.
	 *
	 * @param \WC_Product      $product Product to add to the order.
	 * @param \WC_Order $order Instance of order to add the product to.
	 */
	protected function edit_order( $product, $order ) {

		$add_product_args = array();

		if ( $this->get_option( 'line_item_name' ) ) {
			$add_product_args['name'] = $this->get_option( 'line_item_name', true );
		}

		if ( $this->get_option( 'line_item_cost' ) ) {
			$add_product_args['subtotal'] = wc_get_price_excluding_tax(
				$product,
				array(
					'price' => $this->get_option( 'line_item_cost', true ),
					'qty'   => $this->get_option( 'quantity' ),
				)
			);
			$add_product_args['total']    = $add_product_args['subtotal'];
		}

		$order->add_product( $product, $this->get_option( 'quantity' ), $add_product_args );
		$this->recalculate_order_totals( $order );
	}


	/**
	 * Get a message to add to the order to record the product being added by this action.
	 *
	 * Helpful for tracing the history of this action by viewing the order's notes.
	 *
	 * @param \WC_Product $product Product being added to the order. Required so its name can be added to the order note.
	 * @return string
	 */
	protected function get_note( $product ) {
		return sprintf( __( '%1$s workflow run: added %2$s to order. (Product ID: %3$d; Workflow ID: %4$d)', 'automatewoo' ), $this->workflow->get_title(), $product->get_name(), $product->get_id(), $this->workflow->get_id() );
	}
}
