<?php

namespace W3Guy;

/**
 * Class Custom_Settings_Api
 */
class Custom_Settings_Page_Api {

	private $db_options;

	private $option_name;

	private $tabs_config;

	/** @var array array config of main settings page */
	private $main_content_config;

	private $sidebar_config;

	public function __construct( $main_content_config = '', $option_name = '' ) {
		$this->db_options          = get_option( $option_name );
		$this->main_content_config = $main_content_config;
	}

	public function option_name( $val ) {
		$this->option_name = $val;
	}

	public function tab( $val ) {
		$this->tabs_config = $val;
	}

	public function main_content( $val ) {
		$this->main_content_config = $val;
	}

	public function sidebar( $val ) {
		$this->sidebar_config = $val;
	}

	/**
	 * array(
	 *  array('url' => '', 'label' => ''),
	 *  array('url' => '', 'label' => ''),
	 *  );
	 *
	 */
	public function settings_page_tab() {
		$args = $this->tabs_config;
		echo '<h2 class="nav-tab-wrapper">';
		if ( is_array( $args ) ) {
			foreach ( $args as $arg ) {
				$url    = esc_url( $arg['url'] );
				$label  = esc_attr( $arg['label'] );
				$active = $this->current_page_url() == $url ? ' nav-tab-active' : null;
				echo "<a href=\"$url\" class=\"nav-tab{$active}\">$label</a>";
			}
		}

		echo '</h2>';
	}


	/**
	 *
	 * array(
	 *      array(
	 *          'section_title' => 'Documentation',
	 *          'content'       => '',
	 *`     );
	 * );
	 *
	 * @param array $args array of sidebar parameters.
	 *
	 * @param string $alignment sidebar alignment. Could be center, left or right.
	 */
	public function setting_page_sidebar() { ?>

		<div id="postbox-container-1" class="postbox-container">
			<div class="meta-box-sortables" style="text-align: center; margin: auto">
				<?php if ( is_array( $this->sidebar_config ) ): ?>
					<?php foreach ( $this->sidebar_config as $arg ) : ?>
						<div class="postbox">
							<div class="handlediv" title="Click to toggle"><br></div>
							<h3 class="hndle ui-sortable-handle">
								<span><?php echo $arg['section_title']; ?></span>
							</h3>

							<div class="inside">
								<?php echo $arg['content']; ?>
							</div>
						</div>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}


	/**
	 * Build the settings page.
	 */
	public function build() {
		$this->persist_plugin_settings();
		?>
		<div class="wrap">
			<div id="icon-options-general" class="icon32"></div>
			<h2><?php _e( 'Extras' ); ?></h2>
			<?php if ( isset( $_GET['settings-update'] ) && ( $_GET['settings-update'] ) ) : ?>
				<div id="message" class="updated notice is-dismissible"><p>
						<strong><?php _e( 'Settings saved.' ); ?></strong></p></div>
			<?php endif; ?>
			<?php $this->settings_page_tab() ?>
			<div id="poststuff" class="ppview">
				<div id="post-body" class="metabox-holder columns-2">
					<div id="post-body-content">
						<div class="meta-box-sortables ui-sortable">
							<form method="post" <?php echo apply_filters( 'wp_cspa_form_tag', '' ); ?>>
								<?php $this->_settings_page_main_content_area(); ?>
							</form>
						</div>
					</div>
					<?php $this->setting_page_sidebar(); ?>
				</div>
			</div>
		</div>
	<?php }


	/**
	 * Get current page URL.
	 *
	 * @return string
	 */
	private function current_page_url() {
		$pageURL = 'http';
		if ( isset( $_SERVER["HTTPS"] ) ) {
			if ( $_SERVER["HTTPS"] == "on" ) {
				$pageURL .= "s";
			}
		}
		$pageURL .= "://";
		if ( $_SERVER["SERVER_PORT"] != "80" ) {
			$pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
		} else {
			$pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
		}

		return $pageURL;
	}


	/**
	 * Main settings page markup.
	 */
	public function _settings_page_main_content_area() {
		$db_options  = $this->db_options;
		$args_arrays = $this->main_content_config;

		// variable declaration
		$html = '';

		if ( is_array( $args_arrays ) ) {
			foreach ( $args_arrays as $args ) {

				if ( ! empty( $args['section_title'] ) ) {
					$html .= $this->_header( $args['section_title'] );
				}

				// remove section title from array to make the argument keys be arrays so it can work with foreach loop
				if ( isset( $args['section_title'] ) ) {
					unset( $args['section_title'] );
				}

				foreach ( $args as $key => $value ) {
					if ( $args[ $key ]['type'] == 'text' ) {
						$html .= $this->_text( $db_options, $key, $args[ $key ] );
					}
					if ( $args[ $key ]['type'] == 'number' ) {
						$html .= $this->_number( $db_options, $key, $args[ $key ] );
					}
					if ( $args[ $key ]['type'] == 'textarea' ) {
						$html .= $this->_textarea( $db_options, $key, $args[ $key ] );
					}
					if ( $args[ $key ]['type'] == 'select' ) {
						$html .= $this->_select( $db_options, $key, $args[ $key ] );
					}
					if ( $args[ $key ]['type'] == 'checkbox' ) {
						$html .= $this->_checkbox( $db_options, $key, $args[ $key ] );
					}
					if ( $args[ $key ]['type'] == 'hidden' ) {
						$html .= $this->_hidden( $db_options, $key, $args[ $key ] );
					}
				}

				$html .= $this->_footer();
			}
		}

		echo $html;
	}


	/**
	 * Renders the text field
	 *
	 * @param array $db_options addons DB options
	 * @param string $key array key of class argument
	 * @param array $args class args
	 *
	 * @return string
	 */
	public function _text( $db_options, $key, $args ) {
		$key         = esc_attr( $key );
		$label       = esc_attr( $args['label'] );
		$description = $args['description'];
		$option_name = $this->option_name;
		ob_start(); ?>
		<tr>
			<th scope="row"><label for="<?php echo $key; ?>"><?php echo $label; ?></label></th>
			<td>
				<input type="text" id="<?php echo $key; ?>" name="<?php echo $option_name, '[', $key, ']'; ?>" class="regular-text" value="<?php echo ! empty( $db_options[ $key ] ) ? $db_options[ $key ] : ''; ?>"/>

				<p class="description"><?php echo $description; ?></p>
			</td>
		</tr>
		<?php
		return ob_get_clean();
	}

	/**
	 * Renders the number text field
	 *
	 * @param array $db_options addons DB options
	 * @param string $key array key of class argument
	 * @param array $args class args
	 *
	 * @return string
	 */
	public function _number( $db_options, $key, $args ) {
		$key         = esc_attr( $key );
		$label       = esc_attr( $args['label'] );
		$description = $args['description'];
		$option_name = $this->option_name;
		ob_start(); ?>
		<tr>
			<th scope="row"><label for="<?php echo $key; ?>"><?php echo $label; ?></label></th>
			<td>
				<input type="number" id="<?php echo $key; ?>" name="<?php echo $option_name, '[', $key, ']'; ?>" class="regular-text" value="<?php echo ! empty( $db_options[ $key ] ) ? $db_options[ $key ] : ''; ?>"/>

				<p class="description"><?php echo $description; ?></p>
			</td>
		</tr>
		<?php
		return ob_get_clean();
	}

	/**
	 * Renders the number text field
	 *
	 * @param array $db_options addons DB options
	 * @param string $key array key of class argument
	 * @param array $args class args
	 *
	 * @return string
	 */
	public function _hidden( $db_options, $key, $args ) {
		$key         = esc_attr( $key );
		$label       = esc_attr( $args['label'] );
		$description = $args['description'];
		$option_name = $this->option_name;
		ob_start(); ?>
		<tr>
			<th scope="row"><label for="<?php echo $key; ?>"><?php echo $label; ?></label></th>
			<td>
				<input type="hidden" id="<?php echo $key; ?>" name="<?php echo $option_name, '[', $key, ']'; ?>" class="regular-text" value="<?php echo ! empty( $db_options[ $key ] ) ? $db_options[ $key ] : ''; ?>"/>

				<p class="description"><?php echo $description; ?></p>
			</td>
		</tr>
		<?php
		return ob_get_clean();
	}


	/**
	 * Renders the textarea field
	 *
	 * @param array $db_options addons DB options
	 * @param string $key array key of class argument
	 * @param array $args class args
	 *
	 * @return string
	 */
	public function _textarea( $db_options, $key, $args ) {
		$key         = esc_attr( $key );
		$label       = esc_attr( $args['label'] );
		$description = $args['description'];
		$rows        = ! empty( $args['rows'] ) ? $args['rows'] : 5;
		$cols        = ! empty( $args['column'] ) ? $args['column'] : '';
		$option_name = $this->option_name;
		ob_start();
		?>
		<tr>
			<th scope="row"><label for="<?php echo $key; ?>"><?php echo $label; ?></label></th>
			<td>
				<textarea rows="<?php echo $rows; ?>" cols="<?php echo $cols; ?>" name="<?php echo $option_name, '[', $key, ']'; ?>" id="<?php echo $key; ?>"><?php echo ! empty( $db_options[ $key ] ) ? stripslashes( $db_options[ $key ] ) : ''; ?></textarea>

				<p class="description"><?php echo $description; ?></p>
			</td>
		</tr>
		<?php
		return ob_get_clean();
	}


	/**
	 * Renders the select dropdown
	 *
	 * @param array $db_options addons DB options
	 * @param string $key array key of class argument
	 * @param array $args class args
	 *
	 * @return string
	 */
	public function _select( $db_options, $key, $args ) {
		$key         = esc_attr( $key );
		$label       = esc_attr( $args['label'] );
		$description = $args['description'];
		$options     = $args['options'];
		$option_name = $this->option_name;
		ob_start() ?>
		<tr>
			<th scope="row"><label for="<?php echo $key; ?>"><?php echo $label; ?></label></th>
			<td>
				<select id="<?php echo $key; ?>" name="<?php echo $option_name, '[', $key, ']'; ?>">
					<?php foreach ( $options as $option_key => $option_value ) : ?>
						<option value="<?php echo $option_key; ?>" <?php ! empty( $db_options[ $key ] ) ? selected( $db_options[ $key ], $option_key ) : '' ?>><?php echo esc_attr( $option_value ); ?></option>
					<?php endforeach; ?>
				</select>

				<p class="description"><?php echo $description; ?></p>
			</td>
		</tr>
		<?php
		return ob_get_clean();
	}


	/**
	 * Renders the checkbox field
	 *
	 * @param array $db_options addons DB options
	 * @param string $key array key of class argument
	 * @param array $args class args
	 *
	 * @return string
	 */
	public function _checkbox( $db_options, $key, $args ) {
		$key            = esc_attr( $key );
		$label          = esc_attr( $args['label'] );
		$description    = $args['description'];
		$checkbox_label = ! empty( $args['checkbox_label'] ) ? esc_attr( $args['checkbox_label'] ) : __( 'Activate', 'wp_cspa' );
		$value          = ! empty( $args['value'] ) ? esc_attr( $args['value'] ) : 'true';
		$option_name    = $this->option_name;
		ob_start();
		?>
		<tr>
			<th scope="row"><label for="<?php echo $key; ?>"><?php echo $label; ?></label></th>
			<td>
				<strong><label for="<?php echo $key; ?>"><?php echo $checkbox_label; ?></label></strong>
				<input type="checkbox" id="<?php echo $key; ?>" name="<?php echo $option_name, '[', $key, ']'; ?>" value="<?php echo $value; ?>" <?php ! empty( $db_options[ $key ] ) ? checked( $db_options[ $key ], $value ) : '' ?> />

				<p class="description"><?php echo $description; ?></p>
			</td>
		</tr>
		<?php
		return ob_get_clean();
	}


	/**
	 * Section header
	 *
	 * @param string $section_title
	 *
	 * @return string
	 */
	public function _header( $section_title ) {
		ob_start();
		?>
		<div class="postbox">
		<div class="handlediv" title="Click to toggle"><br></div>
		<h3 class="hndle ui-sortable-handle"><span><?php echo esc_attr( $section_title ); ?></span></h3>
		<div class="inside">
			<table class="form-table">
		<?php
		return ob_get_clean();
	}

	/**
	 * Helper function to recursively sanitize POSTed data.
	 *
	 * @param $data
	 *
	 * @return string|array
	 */
	public static function sanitize_data( $data ) {
		if ( is_string( $data ) ) {
			return sanitize_text_field( $data );
		}
		$sanitized_data = array();
		foreach ( $data as $key => $value ) {
			if ( is_array( $data[ $key ] ) ) {
				$sanitized_data[ $key ] = self::sanitize_data( $data[ $key ] );
			} else {
				$sanitized_data[ $key ] = sanitize_text_field( $data[ $key ] );
			}
		}

		return $sanitized_data;
	}


	/**
	 * Persist the form data to database.
	 *
	 * @return \WP_Error|Void
	 */
	public function persist_plugin_settings() {
		if ( empty( $_POST ) ) {
			return;
		}

		/**
		 * Return WP_Error object in your validation function/class method hooked to this action.
		 */
		do_action( 'wp_cspa_validate_data', $_POST[ $this->option_name ] );

		$sanitize_callable = apply_filters( 'wp_cspa_santize_callback', 'self::sanitize_data' );

		$sanitized_data = apply_filters( 'wp_cspa_santized_data', call_user_func( $sanitize_callable, $_POST[ $this->option_name ] ) );

		update_option( $this->option_name, $sanitized_data );
	}


	/**
	 * Section footer.
	 *
	 * @return string
	 */
	public function _footer() {
		return '</table>
		<p><input class="button-primary" type="submit" name="save_' . $this->option_name . '" value="Save Changes"></p>
	</div>
</div>';
	}
}
