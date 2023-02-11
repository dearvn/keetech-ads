<?php

namespace WpKeetech\KeetechAds\Databases\Seeder;

use WpKeetech\KeetechAds\Abstracts\DBSeeder;
use WpKeetech\KeetechAds\Common\Keys;
use WpKeetech\KeetechAds\Settings\Setting;

/**
 * Settings Seeder class.
 *
 * Seed the initial settings.
 */
class SettingsSeeder extends DBSeeder {

    /**
     * Run Settings seeder.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function run() {
        // Check if there is already a seeder runs for this plugin.
        $already_seeded = (bool) get_option( Keys::SETTING_SEEDER_RAN, false );
        if ( $already_seeded ) {
            return;
        }

        // Generate settings.
        update_option(
            Setting::SETTING_META_KEY, [
				'enable_ai' => 'no',
				'api_key'   => '',
                'fb_api_key'   => '',
                'fb_secret_key'   => '',
                'fb_token'   => '',
                'page_id'   => '',
                'tw_api_key'   => '',
                'tw_secret_key'   => '',
                'tw_token'   => '',
			], true
        );

        // Update that seeder already runs.
        update_option( Keys::SETTING_SEEDER_RAN, true );
    }
}
