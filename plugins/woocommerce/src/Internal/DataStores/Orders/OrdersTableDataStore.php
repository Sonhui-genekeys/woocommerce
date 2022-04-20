<?php
/**
 * OrdersTableDataStore class file.
 */

namespace Automattic\WooCommerce\Internal\DataStores\Orders;

defined( 'ABSPATH' ) || exit;

/**
 * This class is the standard data store to be used when the custom orders table is in use.
 */
class OrdersTableDataStore extends \Abstract_WC_Order_Data_Store_CPT implements \WC_Object_Data_Store_Interface, \WC_Order_Data_Store_Interface {

	/**
	 * Get the custom orders table name.
	 *
	 * @return string The custom orders table name.
	 */
	public static function get_orders_table_name() {
		global $wpdb;

		return $wpdb->prefix . 'wc_orders';
	}

	/**
	 * Get the order addresses table name.
	 *
	 * @return string The order addresses table name.
	 */
	public static function get_addresses_table_name() {
		global $wpdb;

		return $wpdb->prefix . 'wc_order_addresses';
	}

	/**
	 * Get the orders operational data table name.
	 *
	 * @return string The orders operational data table name.
	 */
	public static function get_operational_data_table_name() {
		global $wpdb;

		return $wpdb->prefix . 'wc_order_operational_data';
	}

	/**
	 * Get the orders meta data table name.
	 *
	 * @return string Name of order meta data table.
	 */
	public static function get_meta_table_name() {
		global $wpdb;

		return $wpdb->prefix . 'wc_orders_meta';
	}

	/**
	 * Get the names of all the tables involved in the custom orders table feature.
	 *
	 * @return string[]
	 */
	public function get_all_table_names() {
		return array(
			$this->get_orders_table_name(),
			$this->get_addresses_table_name(),
			$this->get_operational_data_table_name(),
			$this->get_meta_table_name(),
		);
	}

	public function get_order_table_columns_and_placeholders() {
		return array(
			'id'                   => '%d',
			'status'               => '%s',
			'currency'             => '%s',
			'tax_amount'           => '%f',
			'total_amount'         => '%f',
			'customer_id'          => '%d',
			'billing_email'        => '%s',
			'date_created_gmt'     => '%s',
			'date_updated_gmt'     => '%s',
			'parent_order_id'      => '%d',
			'payment_method'       => '%s',
			'payment_method_title' => '%s',
			'transaction_id'       => '%s',
			'ip_address'           => '%s',
			'user_agent'           => '%s',
		);
	}

	public function get_address_table_columns_and_placeholders() {
		return array(
			'id'           => '%d',
			'order_id'     => '%d',
			'address_type' => '%s',
			'first_name'   => '%s',
			'last_name'    => '%s',
			'company'      => '%s',
			'address_1'    => '%s',
			'address_2'    => '%s',
			'city'         => '%s',
			'state'        => '%s',
			'postcode'     => '%s',
			'country'      => '%s',
			'email'        => '%s',
			'phone'        => '%s',
		);
	}

	public function get_operational_data_table_columns_and_placeholders() {
		return array(
			'id'                          => '%d',
			'order_id'                    => '%d',
			'created_via'                 => '%s',
			'woocommerce_version'         => '%s',
			'prices_include_tax'          => '%d',
			'coupon_usages_are_counted'   => '%d',
			'download_permission_granted' => '%d',
			'cart_hash'                   => '%s',
			'new_order_email_sent'        => '%d',
			'order_key'                   => '%s',
			'order_stock_reduced'         => '%d',
			'date_paid_gmt'               => '%s',
			'date_completed_gmt'          => '%s',
			'shipping_tax_amount'         => '%f',
			'shipping_total_amount'       => '%f',
			'discount_tax_amount'         => '%f',
			'discount_total_amount'       => '%f',
		);
	}

	// TODO: Add methods for other table names as appropriate.

	//phpcs:disable Squiz.Commenting, Generic.Commenting

	public function get_total_refunded( $order ) {
		// TODO: Implement get_total_refunded() method.
		return 0;
	}

	public function get_total_tax_refunded( $order ) {
		// TODO: Implement get_total_tax_refunded() method.
		return 0;
	}

	public function get_total_shipping_refunded( $order ) {
		// TODO: Implement get_total_shipping_refunded() method.
		return 0;
	}

	public function get_order_id_by_order_key( $order_key ) {
		// TODO: Implement get_order_id_by_order_key() method.
		return 0;
	}

	public function get_order_count( $status ) {
		// TODO: Implement get_order_count() method.
		return 0;
	}

	public function get_orders( $args = array() ) {
		// TODO: Implement get_orders() method.
		return array();
	}

	public function get_unpaid_orders( $date ) {
		// TODO: Implement get_unpaid_orders() method.
		return array();
	}

	public function search_orders( $term ) {
		// TODO: Implement search_orders() method.
		return array();
	}

	public function get_download_permissions_granted( $order ) {
		// TODO: Implement get_download_permissions_granted() method.
		false;
	}

	public function set_download_permissions_granted( $order, $set ) {
		// TODO: Implement set_download_permissions_granted() method.
	}

	public function get_recorded_sales( $order ) {
		// TODO: Implement get_recorded_sales() method.
		return false;
	}

	public function set_recorded_sales( $order, $set ) {
		// TODO: Implement set_recorded_sales() method.
	}

	public function get_recorded_coupon_usage_counts( $order ) {
		// TODO: Implement get_recorded_coupon_usage_counts() method.
		return false;
	}

	public function set_recorded_coupon_usage_counts( $order, $set ) {
		// TODO: Implement set_recorded_coupon_usage_counts() method.
	}

	public function get_order_type( $order_id ) {
		// TODO: Implement get_order_type() method.
		return 'shop_order';
	}

	/**
	 * @param \WC_Order $order
	 */
	public function read( &$order ) {
		if ( ! $order->get_id() ) {
			throw new \Exception( __( 'ID must be set for an order to be read', 'woocommerce' ) );
		}
		// TODO: Load from cache if available, and return early.
		$order_data = $this->get_order_data_for_id( $order->get_id() );
		$order->set_props(
			array(
				'status'        => $order_data->status,
				'currency'      => $order_data->currency,
				'cart_tax'      => $order_data->tax_amount,
				'total'         => $order_data->total_amount,
				'customer_id'   => $order_data->customer_id,
				'billing_email' => $order_data->billing_email,
				'date_created' => $order_data->date_created_gmt,
				'date_modified' => $order_data->date_updated_gmt,
				'parent_id' => $order_data->parent_order_id,
				'payment_method' => $order_data->payment_method,
				'payment_method_title' => $order_data->payment_method_title,
				'transaction_id' => $order_data->transaction_id,
				'customer_ip_address' => $order_data->ip_address,
				'customer_user_agent' => $order_data->user_agent,
				'billing' => array(

				),
				'shipping' => array(

				),
			)
		);

		// TODO: Set cache.
	}

	private function get_order_data_for_id( $id ) {
		$results = $this->get_order_data_for_ids( array( $id ) );

		return $results[0];
	}

	private function get_order_data_for_ids( $ids ) {
		global $wpdb;
		$wpdb->flush();
		$order_table_query = $this->get_order_table_select_statement();
		$id_placeholder    = implode( ', ', array_fill( 0, count( $ids ), '%d' ) );

		return $wpdb->get_results(
			$wpdb->prepare(
				"$order_table_query WHERE wc_order.id in ( $id_placeholder )",
				$ids
			)
		);
	}

	private function get_order_table_select_statement() {
		$order_table                  = $this::get_orders_table_name();
		$order_table_alias            = 'wc_order';
		$select_clause                = "$order_table_alias." . implode( ", $order_table_alias.", array_keys( $this->get_order_table_columns_and_placeholders() ) );
		$billing_address_table_alias  = 'address_billing';
		$shipping_address_table_alias = 'address_shipping';
		$op_data_table_alias          = 'order_operational_data';
		list( $billing_address_select_clause, $billing_address_join_clause ) = $this->join_billing_address_table_to_order_query( $order_table_alias, $billing_address_table_alias, );
		list( $shipping_address_select_clause, $shipping_address_join_clause ) = $this->join_shipping_address_table_to_order_query( $order_table_alias, $shipping_address_table_alias );
		list( $operational_data_select_clause, $operational_data_join_clause ) = $this->join_operational_data_table_to_order_query( $order_table_alias, $op_data_table_alias );

		return
			"
SELECT $select_clause, $billing_address_select_clause, $shipping_address_select_clause, $operational_data_select_clause
FROM $order_table $order_table_alias
LEFT JOIN $billing_address_join_clause
LEFT JOIN $shipping_address_join_clause
LEFT JOIN $operational_data_join_clause
";
	}

	private function join_billing_address_table_to_order_query( $order_table_alias, $address_table_alias ) {
		return $this->join_address_table_order_query( 'billing', $order_table_alias, $address_table_alias );
	}

	private function join_shipping_address_table_to_order_query( $order_table_alias, $address_table_alias ) {
		return $this->join_address_table_order_query( 'shipping', $order_table_alias, $address_table_alias );
	}

	private function join_address_table_order_query( $address_type, $order_table_alias, $address_table_alias ) {
		global $wpdb;
		$address_table = $this::get_addresses_table_name();
		$columns       = array_keys( $this->get_address_table_columns_and_placeholders() );
		list( $select_clause, $join_clause ) = $this->generate_select_and_join_clauses( $order_table_alias, $address_table, $address_table_alias, $columns );
		$join_clause = $wpdb->prepare(
			"$join_clause AND $address_table_alias.address_type = %s",
			$address_type
		);

		return array( $select_clause, $join_clause );
	}

	private function join_operational_data_table_to_order_query( $order_table_alias, $operational_table_alias ) {
		$operational_data_table = $this::get_operational_data_table_name();

		return $this->generate_select_and_join_clauses(
			$order_table_alias,
			$operational_data_table,
			$operational_table_alias,
			array_keys( $this->get_operational_data_table_columns_and_placeholders() ) );
	}

	private function generate_select_and_join_clauses( $order_table_alias, $table, $table_alias, $columns ) {
		// Add aliases to column names so they will be unique when fetching.
		$columns = array_map( function ( $column ) use ( $table_alias ) {
			return "$table_alias.$column as {$table_alias}_$column";
		}, $columns );
		$select_clause = implode( ', ', $columns );
		$join_clause   = "$table $table_alias ON $table_alias.order_id = $order_table_alias.id";

		return array( $select_clause, $join_clause );
	}


	/**
	 * @param \WC_Order $order
	 */
	public function create( &$order ) {
		throw new \Exception( 'Unimplemented' );
	}

	public function update( &$order ) {
		throw new \Exception( 'Unimplemented' );
	}

	public function get_coupon_held_keys( $order, $coupon_id = null ) {
		return array();
	}

	public function get_coupon_held_keys_for_users( $order, $coupon_id = null ) {
		return array();
	}

	public function set_coupon_held_keys( $order, $held_keys, $held_keys_for_user ) {
		throw new \Exception( 'Unimplemented' );
	}

	public function release_held_coupons( $order, $save = true ) {
		throw new \Exception( 'Unimplemented' );
	}

	public function get_stock_reduced( $order ) {
		return false;
	}

	public function set_stock_reduced( $order, $set ) {
		throw new \Exception( 'Unimplemented' );
	}

	public function query( $query_vars ) {
		return array();
	}

	public function get_order_item_type( $order, $order_item_id ) {
		return 'line_item';
	}

	//phpcs:enable Squiz.Commenting, Generic.Commenting

	/**
	 * Get the SQL needed to create all the tables needed for the custom orders table feature.
	 *
	 * @return string
	 */
	public function get_database_schema() {
		$orders_table_name           = $this->get_orders_table_name();
		$addresses_table_name        = $this->get_addresses_table_name();
		$operational_data_table_name = $this->get_operational_data_table_name();
		$meta_table                  = $this->get_meta_table_name();

		$sql = "
CREATE TABLE $orders_table_name (
	id bigint(20) unsigned auto_increment,
	post_id bigint(20) unsigned null,
	status varchar(20) null,
	currency varchar(10) null,
	tax_amount decimal(26,8) null,
	total_amount decimal(26,8) null,
	customer_id bigint(20) unsigned null,
	billing_email varchar(320) null,
	date_created_gmt datetime null,
	date_updated_gmt datetime null,
	parent_order_id bigint(20) unsigned null,
	payment_method varchar(100) null,
	payment_method_title text null,
	transaction_id varchar(100) null,
	ip_address varchar(100) null,
	user_agent text null,
	PRIMARY KEY (id),
	KEY post_id (post_id),
	KEY status (status),
	KEY date_created (date_created_gmt),
	KEY customer_id_billing_email (customer_id, billing_email)
);
CREATE TABLE $addresses_table_name (
	id bigint(20) unsigned auto_increment primary key,
	order_id bigint(20) unsigned NOT NULL,
	address_type varchar(20) null,
	first_name text null,
	last_name text null,
	company text null,
	address_1 text null,
	address_2 text null,
	city text null,
	state text null,
	postcode text null,
	country text null,
	email varchar(320) null,
	phone varchar(100) null,
	KEY order_id (order_id),
	KEY address_type_order_id (address_type, order_id)
);
CREATE TABLE $operational_data_table_name (
	id bigint(20) unsigned auto_increment primary key,
	order_id bigint(20) unsigned NULL,
	created_via varchar(100) NULL,
	woocommerce_version varchar(20) NULL,
	prices_include_tax tinyint(1) NULL,
	coupon_usages_are_counted tinyint(1) NULL,
	download_permission_granted tinyint(1) NULL,
	cart_hash varchar(100) NULL,
	new_order_email_sent tinyint(1) NULL,
	order_key varchar(100) NULL,
	order_stock_reduced tinyint(1) NULL,
	date_paid_gmt datetime NULL,
	date_completed_gmt datetime NULL,
	shipping_tax_amount decimal(26, 8) NULL,
	shipping_total_amount decimal(26, 8) NULL,
	discount_tax_amount decimal(26, 8) NULL,
	discount_total_amount decimal(26, 8) NULL,
	KEY order_id (order_id),
	KEY order_key (order_key)
);
CREATE TABLE $meta_table (
	id bigint(20) unsigned auto_increment primary key,
	order_id bigint(20) unsigned null,
	meta_key varchar(255),
	meta_value text null,
	KEY meta_key_value (meta_key, meta_value(100))
);
";

		return $sql;
	}
}
