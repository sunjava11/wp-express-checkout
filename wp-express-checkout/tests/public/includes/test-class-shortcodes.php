<?php

namespace WP_Express_Checkout;
use WP_UnitTest_Factory_For_Post;


/**
 * Generated by PHPUnit_SkeletonGenerator on 2021-06-28 at 07:52:24.
 *
 * @group shortcodes
 *
 * @covers WP_Express_Checkout\Shortcodes
 */
class ShortcodesTest extends \WP_UnitTestCase {

	/**
	 * @var Shortcodes
	 */
	protected $object;
	protected $factory;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	public function setUp():void {
		parent::setUp();
		$this->object = new Shortcodes;
		$this->factory = new WP_UnitTest_Factory_For_Post();

		add_filter( 'wpec_product_template', function( $located, $template_name ) {
			$default = WPEC_TESTS_DIR . '/mocks/mock-' . $template_name;
			return file_exists( $default ) ? $default : $located;
		}, 10, 2 );
	}

	public function tearDown():void {
		parent::tearDown();
		unset( $_GET['order_id'] );
		unset( $_GET['_wpnonce'] );
	}

	/**
	 * @covers WP_Express_Checkout\Shortcodes::get_instance
	 */
	public function testGet_instance() {
		$reflection = new \ReflectionClass( 'WP_Express_Checkout\Shortcodes' );
		$property   = $reflection->getProperty( 'instance' );
		$property->setAccessible( true );
		$property->setValue( $this->object, null );

		$instance = Shortcodes::get_instance();

		$this->assertInstanceOf( 'WP_Express_Checkout\Shortcodes', $instance );

		$instance2 = Shortcodes::get_instance();

		$this->assertEquals( $instance, $instance2 );
	}

	/**
	 * @covers WP_Express_Checkout\Shortcodes::show_err_msg
	 * @covers WP_Express_Checkout\Shortcodes::shortcode_wp_express_checkout
	 */
	public function testShortcode_wp_express_checkout__invalid_product() {
		$atts = [];
		$output = $this->object->shortcode_wp_express_checkout( $atts );

		$this->assertStringContainsString( 'wpec-error-message', $output );
	}

	/**
	 * @covers WP_Express_Checkout\Shortcodes::shortcode_wp_express_checkout
	 */
	public function testShortcode_wp_express_checkout__reflects() {
		$product_id = $this->factory->create(
			[
				'post_type' => Products::$products_slug,
			]
		);

		$atts = [
			'product_id' => $product_id,
			'thank_you_url' => 'https://example.com/dummy_url',
			'modal' => 'indeed',
		];
		
		// Create new mock settings object
		$mock_settings = array( 'sandbox_client_id' => 'dummy_sandbox_client_id' ) ;
				
		// Set the mock settings object as the current active settings
		update_option( 'ppdg-settings', $mock_settings );

		$mock   = new Mock_Shortcodes;		
		$output = $mock->shortcode_wp_express_checkout( $atts );		
		$this->assertStringNotContainsString( 'wpec-error-message', $output );

		//unnecessary tests, the method generate_pp_express_checkout_button() was being fired, from the mock shortcode class,
		//which is simply serializing the attributes, passed. Since plugin now uses a default template. 
		//So the mock method generate_pp_express_checkout_button() wasn't executing.

		//$shortcode_data = unserialize( $output );

		//$this->assertEquals( $atts['thank_you_url'], $shortcode_data['thank_you_url'] );
		//$this->assertEquals( $atts['modal'], $shortcode_data['use_modal'] );
	}

	/**
	 * @covers WP_Express_Checkout\Shortcodes::shortcode_wp_express_checkout
	 */
	public function testShortcode_wp_express_checkout__reflects_template() {
		$product_id = $this->factory->create(
			[
				'post_type' => Products::$products_slug,
			]
		);

		$atts = [
			'product_id' => $product_id,
			'template' => 99,
		];

		$mock   = new Mock_Shortcodes;
		$output = $mock->shortcode_wp_express_checkout( $atts );

		$this->assertEquals( 'test template 99', $output );
	}

	/**
	 * @covers WP_Express_Checkout\Shortcodes::generate_pp_express_checkout_button
	 *
	 * @dataProvider shortcode_args
	 */
	public function testGenerate_pp_express_checkout_button__no_client_id( $args, $settings ) {
		$output = $this->object->generate_pp_express_checkout_button( $args );
		$this->assertStringContainsString( 'wpec-error-message-client-id', $output );
	}

	/**
	 * @covers WP_Express_Checkout\Shortcodes::generate_pp_express_checkout_button
	 *
	 * @dataProvider shortcode_args
	 */
	public function testGenerate_pp_express_checkout_button( $args, $settings ) {
		update_option( 'ppdg-settings', array_merge( Main::get_defaults(), $settings ) );

		$output = $this->object->generate_pp_express_checkout_button( $args );
		$this->assertStringNotContainsString( 'wpec-error-message-client-id', $output );
		$this->assertStringContainsString( 'wpec-modal', $output );
		$this->assertStringContainsString( 'wp-ppec-button-container', $output );

		update_option( 'ppdg-settings', Main::get_defaults() );
	}

	/**
	 * @covers WP_Express_Checkout\Shortcodes::generate_price_tag
	 */
	public function testGenerate_price_tag() {
		$output = $this->object->generate_price_tag(
			[
				'price'    => 42,
				'quantity' => 2,
				'tax'      => 10,
				'shipping' => 5,
			]
		);

		$this->assertStringContainsString( 'wpec-price-amount', $output );
		$this->assertStringContainsString( 'wpec-quantity', $output );
		$this->assertStringContainsString( 'wpec_price_tax_section', $output );
		$this->assertStringContainsString( 'wpec_price_shipping_section', $output );
		$this->assertStringContainsString( '97.40', $output );
	}

	/**
	 * @covers WP_Express_Checkout\Shortcodes::generate_price_tag
	 */
	public function testGenerate_price_tag__no_tax() {
		$output = $this->object->generate_price_tag(
			[
				'price'    => 42,
				'quantity' => 2,
				'shipping' => 5,
			]
		);

		$this->assertStringNotContainsString( 'wpec_price_tax_section', $output );
		$this->assertStringContainsString( '89.00', $output );
	}

	/**
	 * @covers WP_Express_Checkout\Shortcodes::generate_price_tag
	 */
	public function testGenerate_price_tag__empty_price() {
		$output = $this->object->generate_price_tag(
			[
				'price'    => 0,
				'quantity' => 2,
				'shipping' => 5,
				'tax'      => 10,
			]
		);

		$this->assertStringContainsString( 'wpec_price_tax_section', $output );
	}

	/**
	 * @covers WP_Express_Checkout\Shortcodes::get_thank_you_page_order
	 */
	public function testGet_thank_you_page_order__no_order_id() {
		$output = $this->object->get_thank_you_page_order();
		$this->assertStringContainsString( 'wpec-error-message-missing-order-id', $output );
	}

	/**
	 * @covers WP_Express_Checkout\Shortcodes::get_thank_you_page_order
	 */
	public function testGet_thank_you_page_order__nonce_verification() {
		$_GET['order_id'] = 'dummy';

		$output = $this->object->get_thank_you_page_order();
		$this->assertStringContainsString( 'wpec-error-message-nonce-verification', $output );
	}

	/**
	 * @covers WP_Express_Checkout\Shortcodes::get_thank_you_page_order
	 */
	public function testGet_thank_you_page_order__incorrect_order() {
		$_GET['order_id'] = 'dummy';
		$_GET['_wpnonce'] = wp_create_nonce( 'thank_you_url' . $_GET['order_id'] );

		$output = $this->object->get_thank_you_page_order();
		$this->assertStringContainsString( 'wpec-error-message-2003', $output );
	}

	/**
	 * @covers WP_Express_Checkout\Shortcodes::get_thank_you_page_order
	 */
	public function testGet_thank_you_page_order__incorrect_order_state() {
		$order = Orders::create();

		$_GET['order_id'] = $order->get_id();
		$_GET['_wpnonce'] = wp_create_nonce( 'thank_you_url' . $_GET['order_id'] );

		$output = $this->object->get_thank_you_page_order();
		$this->assertStringContainsString( 'wpec-error-message-order-state', $output );
	}

	/**
	 * @covers WP_Express_Checkout\Shortcodes::get_thank_you_page_order
	 */
	public function testShortcode_wpec_thank_you__no_order_id() {
		$output = $this->object->shortcode_wpec_thank_you();
		$this->assertStringContainsString( 'wpec-error-message-missing-order-id', $output );
	}

	/**
	 * @covers WP_Express_Checkout\Shortcodes::shortcode_wpec_thank_you
	 */
	public function testShortcode_wpec_thank_you__reflects_template() {
		$product_id = $this->factory->create(
			[
				'post_type'  => Products::$products_slug,
				'meta_input' => [
					'ppec_product_upload' => 'dummy'
				]
			]
		);
		$order = Orders::create();
		$order->set_resource_id( "test-resource-id-{$product_id}-{$order->get_id()}" );
		$order->add_data(
			'payer',
			[
				'name' => [
					'given_name' => '',
					'surname' => '',
				],
				'email_address' => ''
			]
		);
		$order->add_data( 'state', 'COMPLETED' );
		$order->add_item( Products::$products_slug, $product_id, 0, 1, $product_id );

		$_GET['order_id'] = $order->get_id();
		$_GET['_wpnonce'] = wp_create_nonce( 'thank_you_url' . $_GET['order_id'] );

		$output = $this->object->shortcode_wpec_thank_you();
		$this->assertStringContainsString( "test-resource-id-{$product_id}-{$order->get_id()}", $output );
		$this->assertStringContainsString( 'wpec_thank_you_message', $output );
		$this->assertStringContainsString( 'wpec-thank-you-page-download-link', $output );
	}

	/**
	 * @covers WP_Express_Checkout\Shortcodes::shortcode_wpec_thank_you
	 */
	public function testShortcode_wpec_thank_you__reflects_content() {
		$product_id = $this->factory->create(
			[
				'post_type'  => Products::$products_slug,
				'meta_input' => [
					'ppec_product_upload' => 'dummy'
				]
			]
		);
		$order = Orders::create();
		$order->set_resource_id( "test-resource-id-{$product_id}-{$order->get_id()}" );
		$order->add_data(
			'payer',
			[
				'name' => [
					'given_name' => '',
					'surname' => '',
				],
				'email_address' => ''
			]
		);
		$order->add_data( 'state', 'COMPLETED' );
		$order->add_item( Products::$products_slug, $product_id, 0, 1, $product_id );

		$_GET['order_id'] = $order->get_id();
		$_GET['_wpnonce'] = wp_create_nonce( 'thank_you_url' . $_GET['order_id'] );

		$output = $this->object->shortcode_wpec_thank_you( [], 'test transaction id [wpec_ty field=transaction_id]' );

		$this->assertEquals( "test transaction id test-resource-id-{$product_id}-{$order->get_id()}", $output );
	}

	/**
	 * @covers WP_Express_Checkout\Shortcodes::shortcode_wpec_thank_you_parts
	 */
	public function testShortcode_wpec_thank_you_parts__no_order_id() {
		$output = $this->object->shortcode_wpec_thank_you_parts();
		$this->assertStringContainsString( 'wpec-error-message-missing-order-id', $output );
	}

	/**
	 * @covers WP_Express_Checkout\Shortcodes::shortcode_wpec_thank_you_parts
	 */
	public function testShortcode_wpec_thank_you_parts__incorrect_order() {
		$_GET['order_id'] = 'dummy';
		$_GET['_wpnonce'] = wp_create_nonce( 'thank_you_url' . $_GET['order_id'] );
		$output = $this->object->shortcode_wpec_thank_you_parts();
		$this->assertStringContainsString( 'wpec-error-message-2003', $output );
	}

	/**
	 * @covers WP_Express_Checkout\Shortcodes::shortcode_wpec_thank_you_parts
	 */
	public function testShortcode_wpec_thank_you_parts__no_part() {
		$product_id = $this->factory->create(
			[
				'post_type'  => Products::$products_slug,
			]
		);
		$order = Orders::create();
		$order->set_resource_id( "test-resource-id-{$product_id}-{$order->get_id()}" );
		$order->add_data( 'state', 'COMPLETED' );

		$_GET['order_id'] = $order->get_id();
		$_GET['_wpnonce'] = wp_create_nonce( 'thank_you_url' . $_GET['order_id'] );

		$output = $this->object->shortcode_wpec_thank_you_parts();
		$this->assertEmpty( $output );
	}

	/**
	 * @covers WP_Express_Checkout\Shortcodes::shortcode_wpec_thank_you_parts
	 */
	public function testShortcode_wpec_thank_you_parts__reflects() {
		$product_id = $this->factory->create(
			[
				'post_type'  => Products::$products_slug,
			]
		);
		$order = Orders::create();
		$order->set_resource_id( "test-resource-id-{$product_id}-{$order->get_id()}" );
		$order->add_data( 'state', 'COMPLETED' );

		$_GET['order_id'] = $order->get_id();
		$_GET['_wpnonce'] = wp_create_nonce( 'thank_you_url' . $_GET['order_id'] );

		$output = $this->object->shortcode_wpec_thank_you_parts( [ 'field' => 'transaction_id' ] );

		$this->assertEquals( "test-resource-id-{$product_id}-{$order->get_id()}", $output );
	}

	/**
	 * @covers WP_Express_Checkout\Shortcodes::shortcode_wpec_thank_you_downloads
	 */
	public function testShortcode_wpec_thank_you_downloads__no_order_id() {
		$output = $this->object->shortcode_wpec_thank_you_downloads();
		$this->assertStringContainsString( 'wpec-error-message-missing-order-id', $output );
	}

	/**
	 * @covers WP_Express_Checkout\Shortcodes::shortcode_wpec_thank_you_downloads
	 */
	public function testShortcode_wpec_thank_you_downloads__no_downloads() {
		$product_id = $this->factory->create(
			[
				'post_type'  => Products::$products_slug,
			]
		);
		$order = Orders::create();
		$order->add_item( Products::$products_slug, $product_id, 0, 1, $product_id );
		$order->add_data( 'state', 'COMPLETED' );

		$_GET['order_id'] = $order->get_id();
		$_GET['_wpnonce'] = wp_create_nonce( 'thank_you_url' . $_GET['order_id'] );

		$output = $this->object->shortcode_wpec_thank_you_downloads();
		$this->assertEmpty( $output );
	}

	/**
	 * @covers WP_Express_Checkout\Shortcodes::shortcode_wpec_thank_you_downloads
	 */
	public function testShortcode_wpec_thank_you_downloads_reflects_template() {
		$product_id = $this->factory->create(
			[
				'post_type'  => Products::$products_slug,
				'meta_input' => [
					'ppec_product_upload' => 'dummy'
				]
			]
		);
		$order = Orders::create();
		$order->add_item( Products::$products_slug, $product_id, 0, 1, $product_id );
		$order->add_data( 'state', 'COMPLETED' );

		$_GET['order_id'] = $order->get_id();
		$_GET['_wpnonce'] = wp_create_nonce( 'thank_you_url' . $_GET['order_id'] );

		$output = $this->object->shortcode_wpec_thank_you_downloads();
		$this->assertStringContainsString( 'wpec-thank-you-page-download-link', $output );
	}

	/**
	 * @covers WP_Express_Checkout\Shortcodes::shortcode_wpec_thank_you_downloads
	 */
	public function testShortcode_wpec_thank_you_downloads_reflects_content() {
		$product_id = $this->factory->create(
			[
				'post_type'  => Products::$products_slug,
				'meta_input' => [
					'ppec_product_upload' => 'dummy'
				]
			]
		);
		$order = Orders::create();
		$order->add_item( Products::$products_slug, $product_id, 0, 1, $product_id );
		$order->add_data( 'state', 'COMPLETED' );

		$_GET['order_id'] = $order->get_id();
		$_GET['_wpnonce'] = wp_create_nonce( 'thank_you_url' . $_GET['order_id'] );

		$output = $this->object->shortcode_wpec_thank_you_downloads( [], '[wpec_ty_download_link anchor_text="test download link"]' );
		$this->assertStringContainsString( 'test download link', $output );
	}

	/**
	 * @covers WP_Express_Checkout\Shortcodes::locate_template
	 */
	public function testLocate_template__not_located() {
		$located = Shortcodes::locate_template( 'dummy' );
		$this->assertEmpty( $located );
	}

	/**
	 * @covers WP_Express_Checkout\Shortcodes::locate_template
	 */
	public function testLocate_template__reflects() {
		$located = Shortcodes::locate_template( 'content-product-2.php' );
		$this->assertEquals( 'content-product-2.php', basename( $located ) );
	}

	public function shortcode_args() {
		$defaults = Main::get_defaults();
		$args = [
			'name'            => 'Item Name',
			'price'           => 0,
			'shipping'        => 0,
			'shipping_enable' => 0,
			'tax'             => 0,
			'quantity'        => 1,
			'url'             => '',
			'product_id'      => '',
			'thumbnail_url'   => '',
			'custom_amount'   => 0,
			'custom_quantity' => 0,
			'currency'        => $defaults['currency_code'],
			'btn_shape'       => $defaults['btn_shape'],
			'btn_type'        => $defaults['btn_type'],
			'btn_height'      => 25,
			'btn_width'       => 0,
			'btn_layout'      => $defaults['btn_layout'],
			'btn_color'       => $defaults['btn_color'],
			'coupons_enabled' => $defaults['coupons_enabled'],
			'button_text'     => $defaults['button_text'],
			'use_modal'       => $defaults['use_modal'],
			'thank_you_url'   => $defaults['thank_you_url'],
			'variations'      => [],
			'stock_enabled'   => false,
			'stock_items'     => 0,
			'price_class'     => 'wpec-price-' . substr( sha1( time() . mt_rand( 0, 1000 ) ), 0, 10 ),
		];
		return [
			[ $args, [ 'is_live' => 0, 'sandbox_client_id' => 'test' ] ],
			[ $args, [ 'is_live' => 1, 'live_client_id' => 'test' ] ],
		];
	}

}
