<?php

namespace WP_Express_Checkout;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2021-06-28 at 07:52:49.
 *
 * @covers WP_Express_Checkout\Payment_Processor_Free
 * @group payments
 * @group ajax
 */
class Payment_Processor_FreeTest extends \WP_Ajax_UnitTestCase {

	/**
	 * @var Payment_Processor_Free
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	public function setUp():void {
		parent::setUp();
		remove_all_actions( 'wp_ajax_wpec_process_empty_payment' );
		remove_all_actions( 'wp_ajax_nopriv_wpec_process_empty_payment' );
		$this->object = new Payment_Processor_Free;
	}

	/**
	 * @covers WP_Express_Checkout\Payment_Processor_Free::wpec_process_payment
	 */
	public function testWpec_process_payment() {
		$product_id = $this->factory->post->create( [
			'post_type' => Products::$products_slug,
		] );
		$_POST['wp_ppdg_payment'] = json_decode( file_get_contents( WPEC_TESTS_DIR . '/data/payment-data.json' ), true );
		$_POST['data'] = [
			'id' => 0,
			'product_id' => $product_id,
			'name' => 'test free payment',
			'price'           => 0,
			'currency'        => 'USD',
			'quantity'        => 5,
			'tax'             => 20,
			'shipping'        => 10,
			'shipping_enable' => 1,
			'url'             => 'http://example.com',
			'custom_quantity' => 1,
			'custom_amount'   => 0,
			'product_id'      => $product_id,
			'coupons_enabled' => 1,
			'thank_you_url'   => 'http://example.com/thank_you',
			'total'           => 50,
		];

		set_transient( 'wp-ppdg-test-free-payment', $_POST['data'] );

		$_POST['nonce'] = wp_create_nonce( $_POST['data']['id'] . $product_id );

		try {
			$this->_handleAjax( 'wpec_process_empty_payment' );
		} catch ( \WPAjaxDieContinueException $e ) {
			// We expected this, do nothing.
		}

		$this->assertTrue( isset( $e ) );
		$response = json_decode( $this->_last_response );
		$this->assertNotEmpty( $response->redirect_url );
	}

}
