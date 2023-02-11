<?php

namespace WpKeetech\KeetechAds\Settings;

use WpKeetech\KeetechAds\Traits\InputSanitizer;
use WP_Error;

class Manager {

	use InputSanitizer;

    /**
     * Get settings.
     *
     * @since 1.0.0
     *
     * @return array|object|null
     */
    public function get() {
        return get_option( Setting::SETTING_META_KEY, $this->get_default_settings() );
    }

    /**
     * Create settings.
     *
     * @since 1.0.0
     *
     * @param array $data
     *
     * @return int|WP_Error $id
     */
    public function create( array $data ): ?array {
        // Prepare setting data for database-insertion.
	    $settings_data = $this->prepare_for_database( $data );

        // Create setting now.
	    $updated = update_option( Setting::SETTING_META_KEY, $settings_data, true );

        if ( ! $updated ) {
            return new WP_Error( 'keetech_ads_settings_save_failed', __( 'Failed to update settings', 'keetech-ads' ) );
        }

        /**
         * Fires after settings has been saved.
         *
         * @since 1.0.0
         *
         * @param array $settings_data
         */
        do_action( 'keetech_ads_settings_saved', $settings_data );

        return $this->get();
    }

	/**
	 * @return string[]
	 */
	public function get_default_settings(): array {
		return [
			'enable_ai' => 'no',
			'api_key'   => '',
            'fb_api_key'   => '',
            'fb_secret_key'   => '',
            'fb_token'   => '',
            'page_id'   => '',
            'tw_api_key'   => '',
            'tw_secret_key'   => '',
            'tw_token'   => '',
		];
	}

	private function prepare_for_database( array $data ): array {
		$data = wp_parse_args( $data, $this->get_default_settings() );

		return [
			'enable_ai' => $this->sanitize( $data['enable_ai'], 'text' ),
			'api_key' => $this->sanitize( $data['api_key'], 'text' ),
            'fb_api_key' => $this->sanitize( $data['fb_api_key'], 'text' ),
            'fb_secret_key' => $this->sanitize( $data['fb_secret_key'], 'text' ),
            'fb_token' => $this->sanitize( $data['fb_token'], 'text' ),
            'page_id' => $this->sanitize( $data['page_id'], 'text' ),
            'tw_api_key' => $this->sanitize( $data['tw_api_key'], 'text' ),
            'tw_secret_key' => $this->sanitize( $data['tw_secret_key'], 'text' ),
            'tw_token' => $this->sanitize( $data['tw_token'], 'text' ),
		];
	}
}
