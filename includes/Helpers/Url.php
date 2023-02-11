<?php

namespace WpKeetech\KeetechAds\Helpers;

class Url {


    /**
     * Check if the URL is for keetech-ads page.
     *
     * @since 1.0.0
     *
     * @return bool
     */
    public static function is_keetech_ads_page(): bool {
        return is_admin() && isset( $_GET['page'] ) && sanitize_text_field( wp_unslash( $_GET['page'] ) ) === keetech_ads()::SLUG; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    }

    /**
     * Check if the URL is for new post or edit post.
     *
     * @since 1.0.0
     *
     * @return bool
     */
    public static function is_new_or_edit_post(): bool {
        $current_url = isset( $_SERVER['REQUEST_URI'] ) ? admin_url( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) ) : '';

        $is_new_post_url  = strpos( $current_url, 'post-new.php' ) !== false;
	    $is_edit_post_url = strpos( $current_url, 'post.php' ) !== false;

	    return $is_new_post_url || $is_edit_post_url;
    }
}
