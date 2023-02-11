<?php

/**
 * Plugin Name:       Keetech ADS
 * Description:       Your Virtual AI assistant to make your WordPress content automation journey smooth and beautiful using Open AI.
 * Requires at least: 5.8
 * Requires PHP:      7.2
 * Version:           1.1.0
 * Author:            Maniruzzaman Akash<manirujjamanakash@gmail.com>
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       keetech-ads
 */

defined( 'ABSPATH' ) || exit;

/**
 * Keetech_Ads class.
 *
 * @class Keetech_Ads The class that holds the entire Keetech_Ads plugin
 */
final class Keetech_Ads {
    /**
     * Plugin version.
     *
     * @var string
     */
    const VERSION = '1.1.0';

    /**
     * Plugin slug.
     *
     * @var string
     *
     * @since 1.0.0
     */
    const SLUG = 'keetech-ads';

    /**
     * Holds various class instances.
     *
     * @var array
     *
     * @since 1.0.0
     */
    private $container = array();

    /**
     * Constructor for the KeetechAds class.
     *
     * Sets up all the appropriate hooks and actions within our plugin.
     *
     * @since 1.0.0
     */
    private function __construct() {
        require_once __DIR__ . '/vendor/autoload.php';

        $this->define_constants();

        register_activation_hook( __FILE__, [ $this, 'activate' ] );
        register_deactivation_hook( __FILE__, [ $this, 'deactivate' ] );

        add_action( 'wp_loaded', [ $this, 'flush_rewrite_rules' ] );
        add_action( 'activated_plugin', [ $this, 'activation_redirect' ] );

        $this->init_plugin();
    }

    /**
     * Initializes the keetech_ads() class.
     *
     * Checks for an existing keetech_ads() instance
     * and if it doesn't find one, creates it.
     *
     * @since 1.0.0
     *
     * @return Keetech_Ads|bool
     */
    public static function init() {
        static $instance = false;

        if ( ! $instance ) {
            $instance = new keetech_ads();
        }

        return $instance;
    }

    /**
     * Magic getter to bypass referencing plugin.
     *
     * @since 1.0.0
     *
     * @param $prop
     *
     * @return mixed
     */
    public function __get( $prop ) {
        if ( array_key_exists( $prop, $this->container ) ) {
            return $this->container[ $prop ];
        }

        return $this->{$prop};
    }

    /**
     * Magic isset to bypass referencing plugin.
     *
     * @since 1.0.0
     *
     * @param $prop
     *
     * @return mixed
     */
    public function __isset( $prop ) {
        return isset( $this->{$prop} ) || isset( $this->container[ $prop ] );
    }

    /**
     * Define the constants.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function define_constants() {
        define( 'KEETECH_ADS_VERSION', self::VERSION );
        define( 'KEETECH_ADS_SLUG', self::SLUG );
        define( 'KEETECH_ADS_FILE', __FILE__ );
        define( 'KEETECH_ADS_DIR', __DIR__ );
        define( 'KEETECH_ADS_PATH', dirname( KEETECH_ADS_FILE ) );
        define( 'KEETECH_ADS_INCLUDES', KEETECH_ADS_PATH . '/includes' );
        define( 'KEETECH_ADS_TEMPLATE_PATH', KEETECH_ADS_PATH . '/templates' );
        define( 'KEETECH_ADS_URL', plugins_url( '', KEETECH_ADS_FILE ) );
        define( 'KEETECH_ADS_BUILD', KEETECH_ADS_URL . '/build' );
        define( 'KEETECH_ADS_ASSETS', KEETECH_ADS_URL . '/assets' );
    }

    /**
     * Load the plugin after all plugins are loaded.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function init_plugin() {
        $this->includes();
        $this->init_hooks();

        /**
         * Fires after the plugin is loaded.
         *
         * @since 1.0.0
         */
        do_action( 'keetech_ads_loaded' );
    }

    /**
     * Activating the plugin.
     *
     * @since 1.0.0
     *
     * @return void
     * @throws Exception
     */
    public function activate() {
        // Run the installer to create necessary migrations and seeders.
        $this->install();
    }

    /**
     * Placeholder for deactivation function.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function deactivate() {
        //
    }

    /**
     * Flush rewrite rules after plugin is activated.
     *
     * Nothing being added here yet.
     *
     * @since 1.0.0
     */
    public function flush_rewrite_rules() {
        // fix rewrite rules
    }

    /**
     * Run the installer to create necessary migrations and seeders.
     *
     * @since 1.0.0
     *
     * @return void
     * @throws Exception
     */
    private function install(): void {
        $installer = new \WpKeetech\KeetechAds\Setup\Installer();
        $installer->run();
    }

    /**
     * Include the required classes.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function includes(): void {
	    // Common classes.
        $this->container['assets']   = new WpKeetech\KeetechAds\Assets\Manager();
        $this->container['blocks']   = new WpKeetech\KeetechAds\Blocks\Manager();
        $this->container['rest_api'] = new WpKeetech\KeetechAds\REST\Api();
        $this->container['settings'] = new WpKeetech\KeetechAds\Settings\Manager();
        $this->container['posts']     = new WpKeetech\KeetechAds\Posts\Manager();
        
        // Admin classes.
        if ( $this->is_request( 'admin' ) ) {
            $this->container['admin_menu'] = new WpKeetech\KeetechAds\Admin\Menu();
        }
    }

    /**
     * Redirect to keetech-ads settings page after plugin activation.
     *
     * @since 1.0.0
     *
     * @param $plugin
     * @return void
     */
    public function activation_redirect( $plugin ): void {
        if ( plugin_basename( __FILE__ ) === $plugin ) {
            wp_safe_redirect( admin_url( 'admin.php?page=keetech-ads#/settings' ) );
            exit();
        }
    }

    /**
     * Initialize the hooks.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function init_hooks(): void {
        // Localize our plugin.
        add_action( 'init', [ $this, 'localization_setup' ] );

        // Add the plugin page links.
        add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), [ $this, 'plugin_action_links' ] );
    }

    /**
     * Initialize plugin for localization.
     *
     * @uses load_plugin_textdomain()
     * @uses wp_set_script_translations()
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function localization_setup(): void {
        load_plugin_textdomain( 'keetech-ads', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

        if ( is_admin() ) {
            // Load wp-script translation for keetech-ads-app.
            wp_set_script_translations( 'keetech-ads-app', 'keetech-ads', plugin_dir_path( __FILE__ ) . 'languages/' );
        }
    }

    /**
     * What type of request is this.
     *
     * @since 1.0.0
     *
     * @param string $type admin, ajax, cron or frontend
     *
     * @return bool
     */
    private function is_request( string $type ): bool {
        switch ( $type ) {
            case 'admin':
                return is_admin();

            case 'ajax':
                return defined( 'DOING_AJAX' );

            case 'rest':
                return defined( 'REST_REQUEST' );

            case 'cron':
                return defined( 'DOING_CRON' );

            case 'frontend':
                return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
        }

        return false;
    }

    /**
     * Plugin action links.
     *
     * @param array $links
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function plugin_action_links( $links ): array {
        $links[] = '<a href="' . admin_url( 'admin.php?page=keetech-ads#/settings' ) . '">' . __( 'Settings', 'keetech-ads' ) . '</a>';
        $links[] = '<a href="https://github.com/dearvn/keetech-ads/wiki" target="_blank">' . __( 'Documentation', 'keetech-ads' ) . '</a>';

        return $links;
    }
}

/**
 * Initialize the main plugin.
 *
 * @since 1.0.0
 *
 * @return Keetech_Ads|bool
 */
function keetech_ads() {
    return Keetech_Ads::init();
}

/*
 * Kick-off the plugin.
 *
 * @since 1.0.0
 */
keetech_ads();
