<?php

namespace WpKeetech\KeetechAds\Databases\Seeder;

use WpKeetech\KeetechAds\Abstracts\DBSeeder;
use WpKeetech\KeetechAds\Common\Keys;

/**
 * PostType Seeder class.
 *
 * Seed some fresh emails for initial startup.
 */
class PostTypeSeeder extends DBSeeder {

    /**
     * Run Posts seeder.
     *
     * @since 0.5.0
     *
     * @return void
     */
    public function run() {
        global $wpdb;

        // Check if there is already a seeder runs for this plugin.
        $already_seeded = (bool) get_option( Keys::POST_TYPE_SEEDER_RAN, false );
        if ( $already_seeded ) {
            return;
        }

        // Generate some post_types.
        $post_types = [
            [
                'name'        => 'Post',
                'slug'        => 'post',
                'description' => 'This is a post.',
                'created_at'  => current_datetime()->format( 'Y-m-d H:i:s' ),
                'updated_at'  => current_datetime()->format( 'Y-m-d H:i:s' ),
            ],
            [
                'name'        => 'Image',
                'slug'        => 'image',
                'description' => 'This is a image.',
                'created_at'  => current_datetime()->format( 'Y-m-d H:i:s' ),
                'updated_at'  => current_datetime()->format( 'Y-m-d H:i:s' ),
            ]
        ];

        // Create each of the post_types.
        foreach ( $post_types as $post_type ) {
            $wpdb->insert(
                $wpdb->prefix . 'keetech_post_types',
                $post_type
            );
        }

        // Update that seeder already runs.
        update_option( Keys::POST_TYPE_SEEDER_RAN, true );
    }
}
