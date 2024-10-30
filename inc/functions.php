<?php
/**
 * Functions.php
 *
 * @package  Kelkoogroup_SalesTracking
 * @author   Kelkoo Group
 * @since    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Add lead tag
 */
function kelkoogroup_salestracking_call_leadtag_js() {
    echo '<script async="true" type="text/javascript" src="https://s.kk-resources.com/leadtag.js" ></script>';
}

/**
 * Stores identifiers from URL parameters in user meta or cookies.
 *
 * This function iterates over predefined URL parameters and their associated user meta keys.
 * If any of these parameters are present in the URL, their values are stored in corresponding user meta
 * for logged-in users or in cookies for guests.
 */
function kelkoogroup_salestracking_store_identifiers_from_url() {
    // Define an array of parameters and their associated keys
    $parameters = array(
        'kk' => 'kelkooId',
        'gclid' => 'kk_gclid',
        'kgclid' => 'kk_gclid',
        'msclkid' => 'kk_msclkid'
    );

    // Iterate over the array of parameters
    foreach ($parameters as $param_key => $meta_key) {
        // Check if the parameter is present in the URL
        if (isset($_GET[$param_key])) {
            // Check if the user is logged in
            if (is_user_logged_in()) {
                // Store the value of the parameter in user meta
                $user_id = get_current_user_id();
                update_user_meta($user_id, "kelkoogroup_salestracking_".$meta_key, $_GET[$param_key]);
            } else {
                // Store the value of the parameter in a cookie
                kelkoogroup_salestracking_setcookie($meta_key, $_GET[$param_key], time() + (365 * DAY_IN_SECONDS), COOKIEPATH, COOKIE_DOMAIN);
            }
        }
    }
}

/**
 * Restore identifiers from cookies to user meta upon login.
 *
 * This function checks for cookies set by the plugin and transfers their values to user meta
 * when the user logs in.
 */
function kelkoogroup_salestracking_restore_identifiers_from_cookies($user_login, $user) {
    // Define an array of meta keys
    $meta_keys = array(
        'kelkoogroup_salestracking_kelkooId',
        'kelkoogroup_salestracking_kk_gclid',
        'kelkoogroup_salestracking_kk_msclkid'
    );

    // Iterate over the array of meta keys
    foreach ($meta_keys as $meta_key) {
        // Check if the cookie is set
        if (isset($_COOKIE[$meta_key])) {
            // Store the value of the cookie in user meta
            update_user_meta($user->ID, $meta_key, $_COOKIE[$meta_key]);
            // Delete the cookie
            kelkoogroup_salestracking_setcookie($meta_key, '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN);
        }
    }
}

/**
 * Custom wrapper for setcookie function.
 *
 * This function acts as a wrapper around the PHP setcookie function to facilitate testing.
 * When testing, the setcookie calls are intercepted and stored in the $_COOKIE superglobal array.
 *
 * @param string $name    The name of the cookie.
 * @param string $value   The value of the cookie.
 * @param int    $expire  The time the cookie expires.
 * @param string $path    The path on the server in which the cookie will be available on.
 * @param string $domain  The (sub)domain that the cookie is available to.
 */
function kelkoogroup_salestracking_setcookie($name, $value, $expire, $path, $domain) {
    setcookie($name, $value, [
        'expires' => $expire,
        'path' => $path,
        'domain' => $domain,
        'samesite' => 'Strict'
    ]);
}

add_action( 'wp_head', 'kelkoogroup_salestracking_call_leadtag_js' );
add_action('init', 'kelkoogroup_salestracking_store_identifiers_from_url');
add_action('wp_login', 'kelkoogroup_salestracking_restore_identifiers_from_cookies', 10, 2);
