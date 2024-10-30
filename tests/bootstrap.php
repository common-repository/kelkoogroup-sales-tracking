<?php
$_tests_dir = dirname(__FILE__) . '/../../../../../tests/phpunit';

if ( ! file_exists( $_tests_dir . '/includes/functions.php' ) ) {
    echo "Could not find $_tests_dir/includes/functions.php. Make sure you have cloned the WordPress test suite correctly.";
    exit( 1 );
}

require_once $_tests_dir . '/includes/functions.php';


function _manually_load_plugin() {
    // Load WooCommerce
    require dirname(__FILE__) . '/../../woocommerce/woocommerce.php';
    // Load KST plugin
    require dirname(__FILE__) . '/../woocommerce-kelkoogroup-salestracking.php';
}

tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

require $_tests_dir . '/includes/bootstrap.php';

