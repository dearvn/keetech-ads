<?php

namespace WpKeetech\KeetechAds\Databases\Seeder;

/**
 * Database Seeder class.
 *
 * It'll seed all the seeders.
 */
class Manager {

    /**
     * Run the database seeders.
     *
     * @since 1.0.0
     *
     * @return void
     * @throws \Exception
     */
    public function run() {
        $seeder_classes = [
            \WpKeetech\KeetechAds\Databases\Seeder\SettingsSeeder::class,
            \WpKeetech\KeetechAds\Databases\Seeder\PostsSeeder::class,
            \WpKeetech\KeetechAds\Databases\Seeder\PostTypeSeeder::class,
        ];

        foreach ( $seeder_classes as $seeder_class ) {
            $seeder = new $seeder_class();
            $seeder->run();
        }
    }
}
