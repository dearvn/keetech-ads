<?php

namespace WpKeetech\KeetechAds\Databases\Seeder;

use WpKeetech\KeetechAds\Abstracts\DBSeeder;
use WpKeetech\KeetechAds\Common\Keys;

/**
 * Posts Seeder class.
 *
 * Seed some fresh emails for initial startup.
 */
class PostsSeeder extends DBSeeder {

    /**
     * Run Posts seeder.
     *
     * @since 0.3.0
     *
     * @return void
     */
    public function run() {
        global $wpdb;

        // Check if there is already a seeder runs for this plugin.
        $already_seeded = (bool) get_option( Keys::POST_SEEDER_RAN, false );
        if ( $already_seeded ) {
            return;
        }

        // Generate some posts.
        $posts = [
            [
                'title'       => 'First Post Post',
                'slug'        => 'first-post-post',
                'description' => 'This is a simple post post.',
                'is_active'   => 1,
                'company_id'  => 1,
                'post_type_id' => 1,
                'created_by'  => get_current_user_id(),
                'created_at'  => current_datetime()->format( 'Y-m-d H:i:s' ),
                'updated_at'  => current_datetime()->format( 'Y-m-d H:i:s' ),
            ],
        ];

        // Create each of the posts.
        foreach ( $posts as $post ) {
            $wpdb->insert(
                $wpdb->prefix . 'keetech_posts',
                $post
            );
        }

        // Update that seeder already runs.
        update_option( Keys::POST_SEEDER_RAN, true );
    }
}
