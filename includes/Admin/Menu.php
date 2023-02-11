<?php

namespace WpKeetech\KeetechAds\Admin;

/**
 * Admin Menu class.
 *
 * Responsible for managing admin menus.
 */
class Menu {

    /**
     * Constructor.
     *
     * @since 1.0.0
     */
    public function __construct() {
        add_action( 'admin_menu', [ $this, 'init_menu' ] );
    }

    /**
     * Init Menu.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function init_menu(): void {
        global $submenu;

        $slug          = KEETECH_ADS_SLUG;
        $menu_position = 50;
        $capability    = 'manage_options';
        $logo_icon     = KEETECH_ADS_ASSETS . '/images/logo-icon.png';

        add_menu_page( esc_attr__( 'Keetech ADS', 'keetech-ads' ), esc_attr__( 'Keetech ADS', 'keetech-ads' ), $capability, $slug, [ $this, 'plugin_page' ], $logo_icon, $menu_position );

        if ( current_user_can( $capability ) ) {
            $submenu[ $slug ][] = [ esc_attr__( 'Dashboard', 'keetech-ads' ), $capability, 'admin.php?page=' . $slug . '#/' ]; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
            $submenu[ $slug ][] = [ esc_attr__( 'Settings', 'keetech-ads' ), $capability, 'admin.php?page=' . $slug . '#/settings' ]; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
            $submenu[ $slug ][] = [ esc_attr__( 'Ads', 'keetech-ads' ), $capability, 'admin.php?page=' . $slug . '#/posts' ]; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

        }
    }

    /**
     * Render the plugin page.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function plugin_page(): void {
        require_once KEETECH_ADS_TEMPLATE_PATH . '/app.php';
    }
}
