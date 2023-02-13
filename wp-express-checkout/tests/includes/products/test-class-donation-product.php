<?php

namespace WP_Express_Checkout\Products;

use WP_Express_Checkout\Products;
use WP_UnitTestCase;
use WP_UnitTest_Factory_For_Post;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2021-08-18 at 07:03:18.
 *
 * @group products
 *
 * @covers WP_Express_Checkout\Products\Donation_Product
 */
class Donation_ProductTest extends WP_UnitTestCase {

	/**
	 * @var Donation_Product
	 */
	protected $object;
	protected $factory;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	public function setUp():void {
		$this->factory = new WP_UnitTest_Factory_For_Post();
		$post = $this->factory->create_and_get(
			[
				'post_type' => Products::$products_slug,
				'meta_input' => [
					'wpec_product_type'       => 'donation',
					'ppec_product_price'      => 10,
					'wpec_product_min_amount' => 20,
				],
			]
		);
		$this->object = new Donation_Product( $post );
	}

	/**
	 * @covers WP_Express_Checkout\Products\Donation_Product::get_price
	 */
	public function testGet_price() {
		$this->assertEquals( 20, $this->object->get_price() );
	}

}
