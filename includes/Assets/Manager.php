<?php

namespace WpKeetech\KeetechAds\Assets;

use WpKeetech\KeetechAds\Helpers\Url;

/**
 * Asset Manager class.
 *
 * Responsible for managing all the assets (CSS, JS, Images, Locales).
 */
class Manager {

    /**
     * Constructor.
     *
     * @since 1.0.0
     */
    public function __construct() {
        add_action( 'init', [ $this, 'register_all_scripts' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
    }

    /**
     * Register all scripts and styles.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function register_all_scripts() {
        $this->register_styles( $this->get_styles() );
        $this->register_scripts( $this->get_scripts() );
        $this->localize_script();
    }

    /**
     * Get all styles.
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function get_styles(): array {
        return [
            'keetech-ads-custom-css' => [
                'src'     => KEETECH_ADS_ASSETS . '/css/style.css',
                'version' => KEETECH_ADS_VERSION,
                'deps'    => [],
            ],
            'keetech-ads-css' => [
                'src'     => KEETECH_ADS_BUILD . '/index.css',
                'version' => KEETECH_ADS_VERSION,
                'deps'    => [],
            ],
        ];
    }

    /**
     * Get all scripts.
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function get_scripts(): array {
        $dependency = require_once KEETECH_ADS_DIR . '/build/index.asset.php';

        return [
            'keetech-ads-app' => [
                'src'       => KEETECH_ADS_BUILD . '/index.js',
                'version'   => $dependency['version'],
                'deps'      => $dependency['dependencies'],
                'in_footer' => true,
            ],
        ];
    }

    /**
     * Register styles.
     *
     * @since 1.0.0
     *
     * @param array $styles
     * @return void
     */
    public function register_styles( array $styles ): void {
        foreach ( $styles as $handle => $style ) {
            wp_register_style( $handle, $style['src'], $style['deps'], $style['version'] );
        }
    }

    /**
     * Register scripts.
     *
     * @since 1.0.0
     *
     * @param array $scripts
     * @return void
     */
    public function register_scripts( array $scripts ): void {
        foreach ( $scripts as $handle => $script ) {
            wp_register_script( $handle, $script['src'], $script['deps'], $script['version'], $script['in_footer'] );
        }
    }

    /**
     * Enqueue admin styles and scripts.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function enqueue_admin_assets(): void {
        // Check if we are on the admin page and page=keetech-ads
        // or in post page
         if ( Url::is_keetech_ads_page() || Url::is_new_or_edit_post() ) {
             wp_enqueue_style( 'keetech-ads-css' );
             wp_enqueue_script( 'keetech-ads-app' );
         }
    }

    /**
     * Localize script for both frontend and backed.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function localize_script(): void {
        wp_enqueue_style( 'keetech-ads-custom-css' );
        wp_enqueue_script( 'keetechAds', KEETECH_ADS_ASSETS . '/js/main.js', filemtime( KEETECH_ADS_DIR . '/assets/js/main.js' ), true );

        $settings = keetech_ads()->settings->get();
        wp_localize_script( 'keetechAds',
            'keetechAds',
            [
                'enableAi'  => $settings['enable_ai'],
                'apiKey'    => $settings['api_key'],
                //'fbApiKey'    => !empty($settings['fb_api_key']) ? $settings['fb_api_key'] : '',
                //'fbSecretKey'    => !empty($settings['fb_secret_key']) ? $settings['fb_secret_key'] : '',
                //'fbToken'    => !empty($settings['fb_token']) ? $settings['fb_token'] : '',
                //'twApiKey'    => !empty($settings['tw_api_key']) ? $settings['tw_api_key'] : '',
                //'twSecretKey'    => !empty($settings['tw_secret_key']) ? $settings['tw_secret_key'] : '',
                //'twToken'    => !empty($settings['tw_token']) ? $settings['tw_token'] : '',
                'urls'      => [
                    'admin'     => admin_url(),
                    'adminPage' => admin_url( 'admin.php' ),
                    'newPost'   => admin_url( 'post-new.php' ),
                ],
                'images'    => [
                    'logoSm' => KEETECH_ADS_ASSETS . '/images/logo-sm.png',
                    'logo'   => KEETECH_ADS_ASSETS . '/images/logo.png',
                ]
            ]
        );
    }
}
