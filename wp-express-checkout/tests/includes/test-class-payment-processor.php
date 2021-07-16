<?php

namespace WP_Express_Checkout;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2021-06-28 at 07:52:34.
 *
 * @group payments
 *
 * @covers WP_Express_Checkout\Payment_Processor
 */
class Payment_ProcessorTest extends \WP_UnitTestCase {

	/**
	 * @var Payment_Processor|Mock_Payment_Processor
	 */
	protected $object;

	/**
	 * @var \MockPHPMailer
	 */
	protected $mailer;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	public function setUp() {
		parent::setUp();
		require_once WPEC_TESTS_DIR . '/mocks/mock-payment-processor.php';

		$product_id = $this->factory->post->create( [
			'post_type' => Products::$products_slug,
		] );

		$this->object = new Mock_Payment_Processor;
		$this->object->_payment_data = json_decode( file_get_contents( WPEC_TESTS_DIR . '/data/payment-data.json' ), true );
		$this->object->_transient['product_id']  = $product_id;
		$this->object->_order_data['product_id'] = $product_id;

		$data = $this->object->_order_data;
		$_REQUEST['nonce'] = wp_create_nonce( $data['id'] . $data['product_id'] );

		reset_phpmailer_instance();

		$this->mailer = $GLOBALS['phpmailer'];

		add_filter( 'wp_doing_ajax', '__return_true' );
		add_filter( 'wp_die_ajax_handler', [ $this, 'get_wp_die_handler' ] );
	}

	/**
	 * @covers WP_Express_Checkout\Payment_Processor::get_instance
	 */
	public function testGet_instance() {
		$reflection = new \ReflectionClass( 'WP_Express_Checkout\Payment_Processor' );
		$property   = $reflection->getProperty( 'instance' );
		$property->setAccessible( true );
		$property->setValue( $this->object, null );

		$instance = Payment_Processor::get_instance();

		$this->assertInstanceOf( 'WP_Express_Checkout\Payment_Processor', $instance );

		$instance2 = Payment_Processor::get_instance();

		$this->assertEquals( $instance, $instance2 );
	}

	/**
	 * @covers WP_Express_Checkout\Payment_Processor::wpec_process_payment
	 */
	public function testWpec_process_payment__3001() {
		$this->expectExceptionCode( 3001 );
		$this->object->_payment_data = array();
		$this->object->wpec_process_payment();
	}

	/**
	 * @covers WP_Express_Checkout\Payment_Processor::wpec_process_payment
	 */
	public function testWpec_process_payment__3002() {
		$this->expectExceptionCode( 3002 );
		$this->object->_order_data = array();
		$this->object->wpec_process_payment();
	}

	/**
	 * @covers WP_Express_Checkout\Payment_Processor::wpec_process_payment
	 */
	public function testWpec_process_payment__3003() {
		$_REQUEST['nonce'] = 'dummy';
		$this->expectExceptionCode( 3003 );
		$this->object->wpec_process_payment();
	}

	/**
	 * @covers WP_Express_Checkout\Payment_Processor::wpec_process_payment
	 */
	public function testWpec_process_payment__3004() {
		$this->object->_transient = [];
		$this->expectExceptionCode( 3004 );
		$this->object->wpec_process_payment();
	}

	/**
	 * @covers WP_Express_Checkout\Payment_Processor::wpec_process_payment
	 */
	public function testWpec_process_payment__2001() {
		add_filter( 'wp_insert_post_empty_content', '__return_true', 10, 1 );
		$this->expectExceptionCode( 2001 );
		$this->object->wpec_process_payment();
	}

	/**
	 * @covers WP_Express_Checkout\Payment_Processor::wpec_process_payment
	 */
	public function testWpec_process_payment__3005() {
		// bad quantity.
		$this->object->_transient['custom_quantity'] = 0;
		$this->object->_transient['quantity'] = 10;
		$this->expectExceptionCode( 3005 );
		$this->object->wpec_process_payment();
	}

	/**
	 * @covers WP_Express_Checkout\Payment_Processor::wpec_process_payment
	 */
	public function testWpec_process_payment__3006() {
		$this->object->_transient['currency'] = 'EUR';
		$this->expectExceptionCode( 3006 );
		$this->object->wpec_process_payment();
	}

	/**
	 * @covers WP_Express_Checkout\Payment_Processor::wpec_process_payment
	 */
	public function testWpec_process_payment__3007() {
		$this->object->_transient['thank_you_url'] = 'dummy';
		$this->expectExceptionCode( 3007 );
		$this->object->wpec_process_payment();
	}

	/**
	 * @covers WP_Express_Checkout\Payment_Processor::wpec_process_payment
	 */
	public function testWpec_process_payment__3008() {
		$this->expectExceptionCode( 3008 );
		$this->object->_payment_data['status'] = '';
		$this->object->wpec_process_payment();
	}

	/**
	 * @covers WP_Express_Checkout\Payment_Processor::wpec_process_payment
	 */
	public function testWpec_process_payment__no_downloads() {
		$this->object->wpec_process_payment();
		$this->assertNotContains( 'download link', $this->mailer->get_sent()->body );
	}

	/**
	 * @covers WP_Express_Checkout\Payment_Processor::wpec_process_payment
	 */
	public function testWpec_process_payment__downloads() {
		update_post_meta( $this->object->_transient['product_id'], 'ppec_product_upload', 'http://example.com/' );
		$this->object->wpec_process_payment();
		$this->assertContains( 'download link', $this->mailer->get_sent()->body );
	}

	/**
	 * @covers WP_Express_Checkout\Payment_Processor::wpec_process_payment
	 */
	public function testWpec_process_payment__emails() {
		update_option( 'ppdg-settings', array_merge( Main::get_defaults(), [ 'send_seller_email' => 1 ] ) );

		$this->object->wpec_process_payment();

		$this->assertEquals( 2, count( $this->mailer->mock_sent ) );
	}

	/**
	 * @covers WP_Express_Checkout\Payment_Processor::wpec_process_payment
	 */
	public function testWpec_process_payment__html_emails() {
		update_option( 'ppdg-settings', array_merge( Main::get_defaults(), [ 'send_seller_email' => 1, 'buyer_email_type' => 'html' ] ) );

		$this->object->wpec_process_payment();
		$this->assertContains( 'text/html', $this->mailer->get_sent()->header );
	}

	/**
	 * @covers WP_Express_Checkout\Payment_Processor::wpec_process_payment
	 */
	public function testWpec_process_payment__no_emails() {
		update_option( 'ppdg-settings', array_merge( Main::get_defaults(), [ 'send_buyer_email' => 0 ] ) );

		$this->object->wpec_process_payment();

		$this->assertEquals( 0, count( $this->mailer->mock_sent ) );
	}

	/**
	 * @covers WP_Express_Checkout\Payment_Processor::wpec_process_payment
	 * @dataProvider payment_args
	 */
	public function testWpec_process_payment__reflects( $transaction_id, $quantity, $tax, $shipping, $total, $cust_amount ) {
		$this->object->_payment_data['id'] = $transaction_id;
		$this->object->_payment_data['purchase_units'][0]['amount']['value'] = $total;
		$this->object->_transient['quantity'] = $quantity;
		$this->object->_transient['custom_quantity'] = 0;
		$this->object->_transient['custom_amount'] = $cust_amount;
		$this->object->_transient['tax'] = $tax;
		$this->object->_transient['shipping'] = $shipping;

		$this->object->wpec_process_payment();

		$this->assertEquals( 1, did_action( 'wpec_create_order' ) );

		$order_post = new \WP_Query( [
			'post_type' => Orders::PTYPE,
			'meta_input' => [
				'key' => 'wpec_order_resource_id', 'value' => $transaction_id
			],
		] );

		$order = Orders::retrieve( $order_post->post->ID );
		$this->assertEquals( $transaction_id, $order->get_resource_id() );
	}

	/**
	 * @covers WP_Express_Checkout\Payment_Processor::wpec_process_payment
	 */
	public function testWpec_process_payment__check_mocked_methods() {
		$_POST['wp_ppdg_payment'] = $this->object->_payment_data;
		$_POST['data'] = $this->object->_order_data;
		$this->expectException( 'WPDieException' );
		$this->expectOutputRegex( '(.*)' );
		$object = new Payment_Processor();

		$object->wpec_process_payment();
	}

	/**
	 * @covers WP_Express_Checkout\Payment_Processor::is_custom_amount
	 */
	public function testIs_custom_amount() {
		$this->assertTrue( $this->object->is_custom_amount( 1 ) );
		$this->assertFalse( $this->object->is_custom_amount( 0 ) );
	}

	/**
	 * transaction id | quantity | tax | shipping | total | custom amount
	 * @return array
	 */
	public function payment_args() {
		return [
			[ 'transactiion 1', 1, 20, 10, 34, 0 ],
			[ 'transactiion 2', 5, 20, 10, 130, 0 ],
			[ 'transactiion 3', 1, 0, 10, 30, 0 ],
			[ 'transactiion 4', 5, 0, 10, 110, 0 ],
			[ 'transactiion 5', 1, 20, 0, 24, 0 ],
			[ 'transactiion 6', 5, 20, 0, 120, 0 ],
			[ 'transactiion 7', 1, 0, 0, 20, 0 ],
			[ 'transactiion 8', 5, 0, 0, 100, 0 ],
			[ 'transactiion 9', 5, 20, 10, 70, 1 ],
		];
	}

}
