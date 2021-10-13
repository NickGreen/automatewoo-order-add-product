<?php

namespace AutomateWoo\Actions\Orders;

use AutomateWoo\Action;
use AutomateWoo\Fields;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Define shared methods to add, remove or update line items on a order.
 *
 * @since 5.4.0
 */
abstract class AbstractEditItem extends Action {


	/**
	 * A order is needed so that it can be edited by instances of this action.
	 *
	 * @var array
	 */
	public $required_data_items = [ 'order' ];


	/**
	 * Flag to define whether the quantity input field should be marked as required.
	 *
	 * @var bool
	 */
	protected $require_quantity_field = true;


	/**
	 * Method to get the item to edit on a order, which might be a
	 * WC_Product, WC_Coupon, or some other data type.
	 *
	 * @return mixed
	 */
	abstract protected function get_object_for_edit();


	/**
	 * Add, remove or update a line item on a order based on a provided object.
	 *
	 * The object to edit on a order can be a WC_Product, WC_Coupon, or some other WooCommerce data type.
	 *
	 * @param mixed            $object WC_Product, WC_Coupon, or some other WooCommerce data type. Will be the same data type as the return value of @see $this->get_object_for_edit().
	 * @param \WC_Order $order Instance of the order being edited by this action.
	 *
	 * @throws \Exception When there is an error.
	 */
	abstract protected function edit_order( $object, $order );


	/**
	 * Get the note to record on the order to record the line item change
	 *
	 * @param mixed $object WC_Product, WC_Coupon, or some other WooCommerce data type. Will be the same data type as the return value of @see $this->get_object_for_edit().
	 * @return string
	 */
	abstract protected function get_note( $object );


	/**
	 * Set the group for all edit actions that extend this class
	 */
	public function load_admin_details() {
		$this->group = __( 'Order', 'automatewoo' );
	}


	/**
	 * Edit the item managed by this class on the order passed in the workflow's trigger
	 *
	 * @throws \Exception When there is an error.
	 */
	public function run() {

		$object = $this->get_object_for_edit();
		$order  = $this->get_order_to_edit();

		if ( ! $object || ! $order ) {
			return;
		}

		$this->edit_order( $object, $order );
		$this->add_note( $object, $order );
	}


	/**
	 * Add a note to record the edit action on the order.
	 *
	 * @param mixed            $object WC_Product, WC_Coupon, or some other WooCommerce data type. Will be the same data type as the return value of @see $this->get_object_for_edit().
	 * @param \WC_Order $order Instance of the order being edited by this action.
	 */
	protected function add_note( $object, $order ) {
		$order->add_order_note( $this->get_note( $object ), false, false );
	}


	/**
	 * Get the order passed in by the workflow's trigger.
	 *
	 * @return \WC_Order|false
	 */
	protected function get_order_to_edit() {
		return $this->workflow->data_layer()->get_order();
	}


	/**
	 * Add a field to enter the product line item quantity to the action's admin input field.
	 *
	 * @param int      $min Minimum value to allow as input. Default 1.
	 * @param null|int $max Maximum value to allow as input. Default null, no maximum.
	 */
	protected function add_quantity_field( $min = 1, $max = null ) {

		$quantity_input = new Fields\Number();

		if ( null !== $max ) {
			$quantity_input->set_max( $max );
		}

		$quantity_input->set_min( $min );
		$quantity_input->set_name( 'quantity' );
		$quantity_input->set_title( __( 'Quantity', 'automatewoo' ) );
		$quantity_input->set_description( $this->get_quantity_field_description() );

		if ( $this->require_quantity_field ) {
			$quantity_input->set_required();
		}

		$this->add_field( $quantity_input );
	}


	/**
	 * Field to set a name on the line item when this action is run
	 */
	protected function add_name_field() {
		$name_field = new Fields\Text();
		$name_field->set_name( 'line_item_name' );
		$name_field->set_title( $this->get_name_field_title() );
		$name_field->set_description( $this->get_name_field_description() );
		$name_field->set_variable_validation();
		$this->add_field( $name_field );
	}


	/**
	 * Get the title to display on the name field for this action
	 */
	protected function get_name_field_title() {
		return __( 'Custom Item Name', 'automatewoo' );
	}


	/**
	 * Get the description to display on the name field for this action
	 */
	protected function get_name_field_description() {
		return __( 'The name to set on the line item.', 'automatewoo' );
	}


	/**
	 * Get the description to display on the quantity field for this action
	 */
	protected function get_quantity_field_description() {
		return '';
	}


	/**
	 * Field to set a price when this action is run
	 */
	protected function add_cost_field() {
		$cost_field = new Fields\Price();
		$cost_field->set_name( 'line_item_cost' );
		$cost_field->set_title( $this->get_cost_field_title() );
		$cost_field->set_description( $this->get_cost_field_description() );
		$cost_field->set_placeholder( __( 'E.g. 10.00', 'automatewoo' ) );
		$cost_field->set_variable_validation();
		$this->add_field( $cost_field );
	}


	/**
	 * Get the title to display on the price field for this action
	 */
	protected function get_cost_field_title() {
		return sprintf( __( 'Custom Item Cost %s', 'automatewoo' ), WC()->countries->ex_tax_or_vat() );
	}


	/**
	 * Get the description to display on the price field for this action
	 */
	protected function get_cost_field_description() {
		return __( 'Optionally set a custom amount, excluding tax, to use for the line item\'s cost. Do not include a currency symbol. Total line item cost will be this amount * line item\'s quantity.', 'automatewoo' );
	}

	/**
	 * Get the description to display on the price field for this action
	 *
	 * @deprecated in 5.1.0
	 *
	 * @return string
	 */
	protected function get_recalculate_coupons_compatibility_text() {
		wc_deprecated_function( __METHOD__, '5.1.0' );
		return __( 'The order\'s coupon discount amount will only be recalculated if you are using WooCommerce version 3.8 or higher.', 'automatewoo' );
	}

	/**
	 * Recalculate a order's totals.
	 *
	 * Recalculates coupons if possible, method was protected until WC 3.8.
	 *
	 * @todo deprecate this
	 *
	 * @param \WC_Order $order
	 *
	 * @since 4.8.0
	 */
	protected function recalculate_order_totals( $order ) {
		if ( is_callable( [ $order, 'recalculate_coupons' ] ) ) {
			$order->recalculate_coupons();
		} else {
			$order->calculate_totals();
		}
	}

}
