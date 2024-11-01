<?php

class Widget_In_Content_Main {
	private $parent;

	/**
	 * Construction.
	 */
	public function __construct( $parent ) {
		$this->parent = $parent;
		add_action( 'plugins_loaded', array( $this, 'setup' ) );
	}

	/**
	 * Set up processing.
	 */
	function setup() {
		add_action( 'widgets_init', array( $this, 'widgets_init' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_filter( 'the_content', array( $this, 'add_widget_content') );
	}

	/**
	 * Enqueue the styles for the current color scheme.
	 */
	function enqueue_scripts() {
		wp_enqueue_style( 'widget-in-content', plugin_dir_url( __FILE__ ) . 'widget-in-content.css', array(), WIDGET_IN_CONTENT_VERSION );
	}

	/**
	 * Initialize widgets.
	 */
	function widgets_init() {
		register_sidebar( array(
			'name' => __( 'Content Top', 'widget-in-content' ),
			'id' => 'widget-in-content-top',
			'description' => __( 'Add widgets here to appear in top of your content.', 'widget-in-content' ),
		) );

		register_sidebar( array(
			'name' => __( 'Content Bottom', 'widget-in-content' ),
			'id' => 'widget-in-content-bottom',
			'description' => __( 'Add widgets here to appear in the bottom of your content.', 'widget-in-content' ),
		) );

		register_sidebar( array(
			'name' => __( 'Content Middle', 'widget-in-content' ),
			'id' => 'widget-in-content-middle',
			'description' => __( 'Add widgets here to appear in the middle of your content.', 'widget-in-content' ),
		) );
	}

	/**
	 * Retrieve dynamic sidebar.
	 *
	 * @param int|string $index Optional, default is 1. Index, name or ID of dynamic sidebar.
	 * string HTML Output.
	 */
	function get_dynamic_sidebar( $sidebar_id ) {
		if ( ! is_user_logged_in() ) {
			$content = wp_cache_get( $sidebar_id, 'widget-in-content' );
			if ( false !== $content ) {
				return $content;
			}
		}

		ob_start();
		dynamic_sidebar( $sidebar_id );
		$content = ob_get_clean();
		if ( ! is_user_logged_in() ) {
			wp_cache_set( $sidebar_id, $content, 'widget-in-content', 5 * MINUTE_IN_SECONDS );
		}

		return $content;
	}

	/**
	 * Inserts widgets into the content.
	 *
	 * @param string $content Content.
	 * @return string Inserted content.
	 */
	function add_widget_content( $content ) {
		$post_types = isset( $this->parent->options['show_post_type'] ) ? $this->parent->options['show_post_type'] : array();
		if ( is_singular( $post_types ) ) {
			if ( is_active_sidebar( 'widget-in-content-middle' ) ) {
				switch ( $this->parent->options['content_middle_position'] ) {
					case 'first-h2-before': $first = true; $header = 'h2'; $before = true; break;
					case 'first-h2-after': $first = true; $header = 'h2'; $before = false; break;
					case 'first-h3-before': $first = true; $header = 'h3'; $before = true; break;
					case 'first-h3-after': $first = true; $header = 'h3'; $before = false; break;
					case 'last-h2-before': $first = false; $header = 'h2'; $before = true; break;
					case 'last-h2-after': $first = false; $header = 'h2'; $before = false; break;
					case 'last-h3-before': $first = false; $header = 'h3'; $before = true; break;
					case 'last-h3-after': $first = false; $header = 'h3'; $before = false; break;
					default: $first = true; $header = 'h2'; $before = true; break;
				}
				$sidebar = '<div class="widget-in-content widget-in-content-middle"><ul>' . $this->get_dynamic_sidebar( 'widget-in-content-middle' ) . '</ul></div>' . "\n";
				$pattern = "/^<{$header}.*?>.+?<\/{$header}>/im";
				if ( preg_match_all( $pattern, $content, $matches ) ) {
					if ( $first ) {
						$pos = strpos( $content, $matches[0][0] );
						$len = strlen( $matches[0][0] );
						$replace = ( $before ? $sidebar . $matches[0][0] : $matches[0][0] . $sidebar );
						$content = substr_replace( $content, $replace, $pos, $len );
					} else {
						$count = count( $matches[0] );
						$pos = strrpos( $content, $matches[0][$count - 1] );
						$len = strlen( $matches[0][$count - 1] );
						$replace = ( $before ? $sidebar . $matches[0][$count - 1] : $matches[0][$count - 1] . $sidebar );
						$content = substr_replace( $content, $replace, $pos, $len );
					}
				}
			}
			if ( is_active_sidebar( 'widget-in-content-top' ) ) {
				$sidebar = '<div class="widget-in-content widget-in-content-top"><ul>' . $this->get_dynamic_sidebar( 'widget-in-content-top' ) . '</ul></div>' . "\n";
				$content = $sidebar . $content;
			}
			if ( is_active_sidebar( 'widget-in-content-bottom' ) ) {
				$sidebar = '<div class="widget-in-content widget-in-content-bottom"><ul>' . $this->get_dynamic_sidebar( 'widget-in-content-bottom' ) . '</ul></div>' . "\n";
				$content = $content . $sidebar;
			}
		}
		return $content;
	}
}
