<?php
use AutomateWoo\Action;
use AutomateWoo\Fields;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Action_Order_Add_Free_Product extends Action {


	/**
	 * The data items required by the action.
	 *
	 * @var array
	 */
	public $required_data_items = array( 'order' );


	/**
	 * Flag to define whether the instance of this action requires a quantity input field to
	 * be displayed on the action's admin UI.
	 *
	 * @var bool
	 */
	protected $load_quantity_field = false;

	/**
	 * Flag to define whether the instance of this action requires a name text input field.
	 *
	 * @var bool
	 */
	protected $load_name_field = false;


	/**
	 * Flag to define whether the instance of this action requires a price input field to
	 * be displayed on the action's admin UI.
	 *
	 * @var bool
	 */
	protected $load_cost_field = false;


	/**
	 * Flag to define whether variable products should be included in search results for the
	 * product select field.
	 *
	 * @var bool
	 */
	protected $allow_variable_products = true;


	/**
	 * Flag to define whether the quantity input field should be marked as required.
	 *
	 * @var bool
	 */
	protected $require_quantity_field = true;

	/**
	 * Knows if admin details have been loaded.
	 *
	 * @var bool
	 */
	protected $has_loaded_admin_details = false;



	/**
	 * Method to load the action's fields.
	 *
	 * TODO make protected method
	 */
	public function load_fields() {
		$this->add_product_select_field();

		if ( $this->load_quantity_field ) {
			$this->add_quantity_field();
		}

		if ( $this->load_name_field ) {
			$this->add_name_field();
		}
	}

	/**
	 * Add a product selection field for this action
	 */
	protected function add_product_select_field() {
		$product_select = new Fields\Product();
		$product_select->set_required();
		$product_select->set_allow_variations( true );
		$product_select->set_allow_variable( $this->allow_variable_products );

		$this->add_field( $product_select );
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
	 * Get the description to display on the quantity field for this action
	 */
	protected function get_quantity_field_description() {
		return '';
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
	 * Method to set the action's admin props.
	 *
	 * Admin props include: title, group and description.
	 */
	protected function load_admin_details() {
		$this->title       = __( 'Add Free Product', 'automatewoo' );
		$this->group       = __( 'Order', 'automatewoo' );
		$this->description = __( 'Add free product to order as a line item. (Caution: Runs after checkout)', 'automatewoo' );
	}

	/**
	 * Loads the action's admin props.
	 */
	protected function maybe_load_admin_details() {
		if ( ! $this->has_loaded_admin_details ) {
			$this->load_admin_details();
			$this->has_loaded_admin_details = true;
		}
	}

	/**
	 * Get the action's title.
	 *
	 * @param bool $prepend_group
	 * @return string
	 */
	public function get_title( $prepend_group = false ) {
		$this->maybe_load_admin_details();
		$group = $this->get_group();
		if ( $this->title ) {
			$title = $this->title;
		} else {
			$title = '';
		}

		if ( $prepend_group && __( 'Other', 'automatewoo' ) !== $group ) {
			return $group . ' - ' . $title;
		}

		return $title;
	}

	/**
	 * Get the action's group.
	 *
	 * @return string
	 */
	public function get_group() {
		$this->maybe_load_admin_details();
		return $this->group ? $this->group : __( 'Other', 'automatewoo' );
	}

	/**
	 * Get the action's description.
	 *
	 * @return string
	 */
	public function get_description() {
		$this->maybe_load_admin_details();
		if ( $this->description ) {
			$description = $this->description;
		} else {
			$description = '';
		}
		return $description;
	}

	/**
	 * Get the action's name.
	 *
	 * @return string
	 */
	public function get_name() {
		if ( $this->name ) {
			$name = $this->name;
		} else {
			$name = '';
		}
		return $name;
	}

	/**
	 * Set the action's name.
	 *
	 * @param string $name
	 */
	public function set_name( $name ) {
		$this->name = $name;
	}

	/**
	 * Get the action's description HTML.
	 *
	 * @return string
	 */
	public function get_description_html() {
		if ( ! $this->get_description() ) {
			return '';
		}

		return '<p class="aw-field-description">' . $this->get_description() . '</p>';
	}

	public function run() {
		if ( $this->workflow->data_layer()->get_order() ) {
			$order = $this->workflow->data_layer()->get_order();
		} else {
			return;
		}
		$product = wc_get_product( $this->get_option( 'product' ) );
		$product->set_price( '' );
		$order->add_product( $product, 1 );
	}
}
