<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitecb54881939c5593641ad428bda777ea
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Sample\\' => 7,
        ),
        'P' => 
        array (
            'PayPalHttp\\' => 11,
            'PayPalCheckoutSdk\\' => 18,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Sample\\' => 
        array (
            0 => __DIR__ . '/..' . '/paypal/paypal-checkout-sdk/samples',
        ),
        'PayPalHttp\\' => 
        array (
            0 => __DIR__ . '/..' . '/paypal/paypalhttp/lib/PayPalHttp',
        ),
        'PayPalCheckoutSdk\\' => 
        array (
            0 => __DIR__ . '/..' . '/paypal/paypal-checkout-sdk/lib/PayPalCheckoutSdk',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'OrdersWPEC' => __DIR__ . '/../..' . '/admin/includes/class-order.php',
        'PPECProducts' => __DIR__ . '/../..' . '/admin/includes/class-products.php',
        'PPECProductsMetaboxes' => __DIR__ . '/../..' . '/admin/includes/class-products-meta-boxes.php',
        'WPECShortcode' => __DIR__ . '/../..' . '/public/includes/class-shortcode-ppec.php',
        'WPEC_Admin' => __DIR__ . '/../..' . '/admin/class-wpec-admin.php',
        'WPEC_Admin_Order_Summary_Table' => __DIR__ . '/../..' . '/admin/includes/class-admin-order-summary-table.php',
        'WPEC_Blocks' => __DIR__ . '/../..' . '/admin/views/blocks.php',
        'WPEC_Coupons_Admin' => __DIR__ . '/../..' . '/admin/includes/class-coupons.php',
        'WPEC_Coupons_Table' => __DIR__ . '/../..' . '/admin/includes/class-coupons-list-table.php',
        'WPEC_Debug_Logger' => __DIR__ . '/../..' . '/includes/class-wpec-debug-logger.php',
        'WPEC_Init_Time_Tasks' => __DIR__ . '/../..' . '/includes/class-wpec-init-time-tasks.php',
        'WPEC_Integrations' => __DIR__ . '/../..' . '/includes/class-wpec-integrations.php',
        'WPEC_Main' => __DIR__ . '/../..' . '/public/class-wpec-main.php',
        'WPEC_Order' => __DIR__ . '/../..' . '/includes/class-order.php',
        'WPEC_Order_List' => __DIR__ . '/../..' . '/admin/includes/class-order-list.php',
        'WPEC_Order_Summary_Table' => __DIR__ . '/../..' . '/includes/class-order-summary-table.php',
        'WPEC_Orders_Metaboxes' => __DIR__ . '/../..' . '/admin/includes/class-orders-meta-boxes.php',
        'WPEC_Post_Type_Content_Handler' => __DIR__ . '/../..' . '/includes/class-wpec-post-type-content-handler.php',
        'WPEC_Process_IPN' => __DIR__ . '/../..' . '/includes/class-wpec-process-ipn.php',
        'WPEC_Process_IPN_Free' => __DIR__ . '/../..' . '/includes/class-wpec-process-ipn-free.php',
        'WPEC_Utility_Functions' => __DIR__ . '/../..' . '/includes/class-wpec-utility-functions.php',
        'WPEC_Variations' => __DIR__ . '/../..' . '/admin/includes/class-variations.php',
        'WPEC_View_Download' => __DIR__ . '/../..' . '/includes/class-wpec-view-download.php',
        'WP_Express_Checkout\\PayPal\\Client' => __DIR__ . '/../..' . '/includes/paypal-client/class-client.php',
        'WP_Express_Checkout\\PayPal\\Request' => __DIR__ . '/../..' . '/includes/paypal-client/class-request.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitecb54881939c5593641ad428bda777ea::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitecb54881939c5593641ad428bda777ea::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitecb54881939c5593641ad428bda777ea::$classMap;

        }, null, ClassLoader::class);
    }
}
