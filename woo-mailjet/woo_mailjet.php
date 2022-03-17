<?php
/**
 * Plugin Name: Woo Mailjet
 * Plugin URI: https://www.wordpress.org
 * Description: Connect Woocommerce with Mailjet.
 * Author: Pratap Dungrani
 * Version: 1.0.0
 * Author URI: 
 * Contributor: 
 * Text Domain: woo-mailjet
 * Requires PHP: 
 * WC requires at least: 
 * WC tested up to:
 */

/**
 * Include the require files
 */
define( 'CUSTOM_WM_EMAIL_PATH', plugin_dir_path( __FILE__ ) );

require_once 'include/wm-email-setting.php';
require_once 'include/wm-subscription-option.php';
require 'mailjet/vendor/autoload.php';


if ( ! class_exists( 'woo_mailjet' ) ) {
	/**
	 * Class
	 */
	class woo_mailjet {

		/**
		 * Default Constructor
		 */
		public function __construct() {

			add_action( 'admin_init', array( &$this, 'check_if_woocommerce_is_active' ) );

			// scripts.
			add_action( 'admin_enqueue_scripts', array( &$this, 'wm_admin_scripts' ) );
			add_action( 'wp_enqueue_scripts', array( &$this, 'wm_front_scripts' ) );

		}


		/**
		 * Check if WooCommerce is active
		 */
		public static function check_woo_is_installed() {
			if ( class_exists( 'WooCommerce' ) ) {
				return true;
			} else {
				return false;
			}
		}

		/**
		 * Check if WooCommerce plugin is active or not.
		 */
		public function check_if_woocommerce_is_active() {
			if ( ! self::check_woo_is_installed() ) {
				if ( is_plugin_active( plugin_basename( __FILE__ ) ) ) {
					deactivate_plugins( plugin_basename( __FILE__ ) );
					add_action( 'admin_notices', array($this, 'wm_disabled_notice' ));
				}
			}
		}

		/**
		 * Display a notice if WooCommerce is deactivated.
		 *
		 */
		public static function wm_disabled_notice() {
			$class   = 'notice notice-error';
			$message = __( 'Woo Mailjet plugin requires WooCommerce installed and activate.', 'woo-mailjet' );

			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_attr( $message ) );
		}


		/**
		 * Enqueue scripts on the admin side
		 */
		public function wm_admin_scripts() {

		}
		

		/**
		 * Enqueue scripts on the front end
		 */
		public function wm_front_scripts() {
			
		}
	}
}
$woo_mailjet = new woo_mailjet();
