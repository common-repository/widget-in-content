<?php
/*
Plugin Name: Widget in Content
Plugin URI: https://xakuro.com/wordpress/
Description: Insert the widget area in the content.
Author: Xakuro
Author URI: https://xakuro.com/
License: GPLv2
Version: 1.0.0
Text Domain: widget-in-content
Domain Path: /languages/
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WIDGET_IN_CONTENT_VERSION', '1.0.0' );

load_plugin_textdomain( 'widget-in-content', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

class Widget_In_Content {
	public $admin, $main, $options;

	/**
	 * Construction.
	 */
	public function __construct() {
		$this->options = get_option( 'widget_in_content_options' );
		if ( $this->options === false ) {
			$this->options = $this->get_default_options();
		}

		if ( is_admin() ) {
			require_once( plugin_dir_path( __FILE__ ) . 'admin.php' );
			$admin = new Widget_In_Content_Admin( $this );
		}

		require_once( plugin_dir_path( __FILE__ ) . 'main.php' );
		$main = new Widget_In_Content_Main( $this );
	}

	/**
	 * Gets the default value of the option.
	 */
	public static function get_default_options() {
		return array(
			'show_post_type' => array( 'post' ),
			'content_middle_position' => 'first-h2-before',
		);
	}

	/**
	 * Plugin activation.
	 */
	public static function activation() {
		$options = get_option( 'widget_in_content_options' );
		if ( $options === false ) {
			add_option( 'widget_in_content_options', Widget_In_Content::get_default_options() );
		}
	}

	/**
	 * Plugin deactivation.
	 */
	public static function uninstall() {
		delete_option( 'widget_in_content_options' );
	}
}

global $widget_in_content;
$widget_in_content = new Widget_In_Content();

register_activation_hook( __FILE__, 'Widget_In_Content::activation' );
register_uninstall_hook( __FILE__, 'Widget_In_Content::uninstall' );
