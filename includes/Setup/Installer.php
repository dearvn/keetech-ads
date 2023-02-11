<?php

namespace WpKeetech\KeetechAds\Setup;

use WpKeetech\KeetechAds\Common\Keys;

/**
 * Class Installer.
 *
 * Install necessary database tables and options for the plugin.
 */
class Installer {

    /**
     * Run the installer.
     *
     * @since 1.0.0
     *
     * @return void
     * @throws \Exception
     */
    public function run(): void {
        // Update the installed version.
        $this->add_version();

        // Register and create tables.
        $this->register_table_names();
        $this->create_tables();

        // Run the database seeders.
        $seeder = new \WpKeetech\KeetechAds\Databases\Seeder\Manager();
        $seeder->run();
    }

    /**
     * Add time and version on DB.
     *
     * @since 1.0.0
     * @since 0.4.1 Fixed #11 - Version Naming.
     *
     * @return void
     */
    public function add_version(): void {
        $installed = get_option( Keys::KEETECH_ADS_INSTALLED );

        if ( ! $installed ) {
            update_option( Keys::KEETECH_ADS_INSTALLED, time() );
        }

        update_option( Keys::KEETECH_ADS_VERSION, KEETECH_ADS_VERSION );
    }

    /**
     * Register table names.
     *
     * @since 0.3.0
     *
     * @return void
     */
    private function register_table_names(): void {
        global $wpdb;

        // Register the tables to wpdb global.
        $wpdb->keetech_post_types = $wpdb->prefix . 'keetech_post_types';
        $wpdb->keetech_posts      = $wpdb->prefix . 'keetech_posts';
    }

    /**
     * Create necessary database tables.
     *
     * @since JOB_PLACE_
     *
     * @return void
     */
    public function create_tables() {
        if ( ! function_exists( 'dbDelta' ) ) {
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        }

        // Run the database table migrations.
        \WpKeetech\KeetechAds\Databases\Migrations\PostTypeMigration::migrate();
        \WpKeetech\KeetechAds\Databases\Migrations\PostsMigration::migrate();
    }
}
