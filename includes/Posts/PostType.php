<?php

namespace WpKeetech\KeetechAds\Posts;

use WpKeetech\KeetechAds\Abstracts\BaseModel;

/**
 * PostType class.
 *
 * @since 0.3.0
 */
class PostType extends BaseModel {

    /**
     * Table Name.
     *
     * @var string
     */
    protected $table = 'keetech_post_types';

    /**
     * Post types item to a formatted array.
     *
     * @since 0.3.0
     *
     * @param object $post_type
     *
     * @return array
     */
    public static function to_array( object $post_type ): array {
        return [
            'id'          => (int) $post_type->id,
            'name'        => $post_type->name,
            'slug'        => $post_type->slug,
            'description' => $post_type->description,
            'created_at'  => $post_type->created_at,
            'updated_at'  => $post_type->updated_at,
        ];
    }
}
