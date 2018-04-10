<?php

namespace P4ML\Controllers\Menu;

if ( ! class_exists( 'Settings_Controller' ) ) {

	/**
	 * Class Settings_Controller
	 *
	 * @package P4ML\Controllers\Menu
	 */
	class Settings_Controller extends Controller {

		/**
		 * Create menu/submenu entry.
		 */
		public function create_admin_menu() {

			if ( current_user_can( 'manage_options' ) ) {
				add_submenu_page(
					P4ML_PLUGIN_SLUG_NAME,
					__( 'ML Settings', 'planet4-medialibrary' ),
					__( 'ML Settings', 'planet4-medialibrary' ),
					'manage_options',
					'mlsettings',
					array( $this, 'prepare_settings' )
				);
			}
			add_action( 'admin_init', array( $this, 'register_settings' ) );
		}

		/**
		 * Render the settings page of the plugin.
		 */
		public function prepare_settings() {
			$this->view->settings( [
				'settings'            => get_option( 'p4ml_main_settings' ),
				'available_languages' => P4ML_LANGUAGES,
				'messages'            => $this->messages,
				'domain'              => 'planet4-medialibrary',
			] );
		}

		/**
		 * Register and store the settings and their data.
		 */
		public function register_settings() {
			$args = array(
				'type'              => 'string',
				'group'             => 'p4ml_main_settings_group',
				'description'       => 'Planet 4 - Media Library settings',
				'sanitize_callback' => array( $this, 'valitize' ),
				'show_in_rest'      => false,
			);
			register_setting( 'p4ml_main_settings_group', 'p4ml_main_settings', $args );
		}

		/**
		 * Validates and sanitizes the settings input.
		 *
		 * @param array $settings The associative array with the settings that are registered for the plugin.
		 *
		 * @return mixed Array if validation is ok, false if validation fails.
		 */
		public function valitize( $settings ) {
			if ( $this->validate( $settings ) ) {
				$this->sanitize( $settings );
			}

			return $settings;
		}

		/**
		 * Validates the settings input.
		 *
		 * @param array $settings The associative array with the settings that are registered for the plugin.
		 *
		 * @return bool
		 */
		public function validate( $settings ): bool {
			$has_errors = false;

			if ( $settings ) {
				if ( isset( $settings['p4ml_login_id'] ) ) {
					add_settings_error(
						'p4ml_main_settings-p4ml_login_id',
						esc_attr( 'p4ml_main_settings-p4ml_login_id' ),
						__( 'Invalid value for Media Library Username', 'planet4-medialibrary' ),
						'error'
					);
					$has_errors = true;
				}
				if ( isset( $settings['p4ml_password'] ) ) {
					add_settings_error(
						'p4ml_main_settings-p4ml_password',
						esc_attr( 'p4ml_main_settings-p4ml_password' ),
						__( 'Invalid value for Media Library Password', 'planet4-medialibrary' ),
						'error'
					);
					$has_errors = true;
				}
			}

			return ! $has_errors;
		}

		/**
		 * Sanitizes the settings input.
		 *
		 * @param array $settings The associative array with the settings that are registered for the plugin.
		 */
		public function sanitize( &$settings ) {
			if ( $settings ) {
				foreach ( $settings as $name => $setting ) {
					$settings[ $name ] = sanitize_text_field( $setting );
				}
			}
		}

		/**
		 * Loads the saved language.
		 */
		public function set_locale(): string {
			$main_settings = get_option( 'p4ml_main_settings' );

			return isset( $main_settings['p4ml_lang'] ) ? $main_settings['p4ml_lang'] : '';
		}
	}
}