<?php

class Widget_In_Content_Admin {
	private $parent;

	/**
	 * Construction.
	 */
	public function __construct( $parent ) {
		$this->parent = $parent;
		add_action( 'plugins_loaded', array( $this, 'setup' ) );
	}

	/**
	 * Set up processing in the administration panel.
	 */
	function setup() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/**
	 * Add a menu to the administration panel.
	 */
	function admin_menu() {
		$settings_page = add_options_page( 'Widget in Content', 'Widget in Content', 'manage_options', 'widget_in_content', array( $this, 'settings_page' ) );
	}

	/**
	 * Output the settings page.
	 */
	function settings_page() {
		echo '<div class="wrap">';
		echo '<h1>' . __( 'Widget in Content Settings', 'widget-in-content' ) . '</h1>';
		echo '<form method="post" action="options.php">';
		settings_fields( 'widget_in_content_group' );
		do_settings_sections( 'widget_in_content_group' );
		submit_button();
		echo '</form>';
		echo '</div>';
	}

	/**
	 * Register the settings.
	 */
	function register_settings() {
		register_setting( 'widget_in_content_group', 'widget_in_content_options', array( $this, 'sanitize' ) );
		add_settings_section( 'widget_in_content_section', '', '__return_false', 'widget_in_content_group' );
		add_settings_field( 'show_post_type', __( 'Type of display widget', 'widget-in-content' ), array( $this, 'field_show_post_type' ), 'widget_in_content_group', 'widget_in_content_section' );
		add_settings_field( 'content_middle', __( 'Position to insert Content Middle', 'widget-in-content' ), array( $this, 'field_content_middle' ), 'widget_in_content_group', 'widget_in_content_section' );
	}

	/**
	 * Register Select show post type field.
	 */
	function field_show_post_type() {
		$checks = isset( $this->parent->options['show_post_type'] ) ? $this->parent->options['show_post_type'] : array();
		$post_types = get_post_types( array( 'public' => true ), 'objects' );
		$fields = array();
		foreach ( $post_types as $post_type ) {
			if ( $post_type->name !== 'attachment' ) {
				$check = in_array( $post_type->name, $checks );
				$field = "<label for=\"show_post_type_{$post_type->name}\">";
				$field .= sprintf( '<input id="show_post_type_%1$s" type="checkbox" class="checkbox" name="widget_in_content_options[show_post_type][]" value="%1$s" %2$s> %3$s (%1$s)', $post_type->name, checked( $check, true, false ), $post_type->label );
				$field .= '</label>';
				$fields[] = $field;
			}
		}
		echo '<fieldset>';
		echo implode( '<br />', $fields );
		echo "</fieldset>\n";
	}

	/**
	 * Register Content Middle field.
	 */
	function field_content_middle() {
		$position = isset( $this->parent->options['content_middle_position'] ) ? $this->parent->options['content_middle_position'] : 'first-h2-before';
		echo '<select name="widget_in_content_options[content_middle_position]" id="field_content_middle_position">';
		echo '<option value="first-h2-before"' . ($position == 'first-h2-before' ? ' selected' : '') . '>' . __( 'Before the first header 2 (h2)', 'widget-in-content' ) . '</option>';
		echo '<option value="first-h2-after"' . ($position == 'first-h2-after' ? ' selected' : '') . '>' . __( 'After the first header 2 (h2)', 'widget-in-content' ) . '</option>';
		echo '<option value="first-h3-before"' . ($position == 'first-h3-before' ? ' selected' : '') . '>' . __( 'Before the first header 3 (h3)', 'widget-in-content' ) . '</option>';
		echo '<option value="first-h3-after"' . ($position == 'first-h3-after' ? ' selected' : '') . '>' . __( 'After the first header 3 (h3)', 'widget-in-content' ) . '</option>';
		echo '<option value="last-h2-before"' . ($position == 'last-h2-before' ? ' selected' : '') . '>' . __( 'Before the last header 2 (h2)', 'widget-in-content' ) . '</option>';
		echo '<option value="last-h2-after"' . ($position == 'last-h2-after' ? ' selected' : '') . '>' . __( 'After the last header 2 (h2)', 'widget-in-content' ) . '</option>';
		echo '<option value="last-h3-before"' . ($position == 'last-h3-before' ? ' selected' : '') . '>' . __( 'Before the last header 3 (h3)', 'widget-in-content' ) . '</option>';
		echo '<option value="last-h3-after"' . ($position == 'last-h3-after' ? ' selected' : '') . '>' . __( 'After the last header 3 (h3)', 'widget-in-content' ) . '</option>';
		echo '</select>';
	}

	/**
	 * Sanitize our setting.
	 */
	function sanitize( $input ) {
		return $input;
	}
}
