<?php

namespace WpKeetech\KeetechAds\Databases\Migrations;

use WpKeetech\KeetechAds\Abstracts\DBMigrator;

/**
 * Posts migration.
 */
class PostsMigration extends DBMigrator {

    /**
     * Migrate the posts table.
     *
     * @since 0.3.0
     *
     * @return void
     */
    public static function migrate() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $schema_posts = "CREATE TABLE IF NOT EXISTS `{$wpdb->keetech_posts}` (
            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            `title` varchar(255) NOT NULL,
            `slug` varchar(255) NOT NULL,
            `company_id` bigint(20) unsigned NULL,
            `post_type_id` int(10) unsigned NULL,
            `fb_post_id` varchar(255) NULL,
            `image_url` mediumtext NULL,
            `description` mediumtext NOT NULL,
            `is_active` tinyint(1) NULL DEFAULT 0,
            `created_by` bigint(20) unsigned NOT NULL,
            `updated_by` bigint(20) unsigned NULL,
            `deleted_by` bigint(20) unsigned NULL,
            `created_at` datetime NOT NULL,
            `updated_at` datetime NOT NULL,
            `deleted_at` datetime NULL,
            PRIMARY KEY (`id`),
            KEY `company_id` (`company_id`),
            UNIQUE KEY `slug` (`slug`),
            KEY `is_active` (`is_active`),
            KEY `post_type_id` (`post_type_id`),
            KEY `post_type_id` (`post_type_id`),
            KEY `created_by` (`created_by`),
            KEY `updated_by` (`updated_by`)
        ) $charset_collate";

        // Create the tables.
        dbDelta( $schema_posts );
    }
}
