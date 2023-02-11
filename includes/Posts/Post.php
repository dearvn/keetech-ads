<?php

namespace WpKeetech\KeetechAds\Posts;

use WpKeetech\KeetechAds\Abstracts\BaseModel;

/**
 * Post class.
 *
 * @since 0.3.0
 */
class Post extends BaseModel {

    /**
     * Table Name.
     *
     * @var string
     */
    protected $table = 'keetech_posts';

    /**
     * Prepare datasets for database operation.
     *
     * @since 0.3.0
     *
     * @param array $request
     * @return array
     */
    public function prepare_for_database( array $data ): array {
        $defaults = [
            'title'       => '',
            'slug'        => '',
            'description' => '',
            'company_id'  => 0,
            'is_active'   => 1,
            'post_type_id' => null,
            'fb_post_id' => null,
            'image_url' => null,
            'created_by'  => get_current_user_id(),
            'created_at'  => current_datetime()->format( 'Y-m-d H:i:s' ),
            'updated_at'  => current_datetime()->format( 'Y-m-d H:i:s' ),
        ];

        $data = wp_parse_args( $data, $defaults );

        // Sanitize template data
        return [
            'title'       => $this->sanitize( $data['title'], 'text' ),
            'slug'        => $this->sanitize( $data['slug'], 'text' ),
            'description' => $this->sanitize( $data['description'], 'block' ),
            'company_id'  => $this->sanitize( $data['company_id'], 'number' ),
            'is_active'   => $this->sanitize( $data['is_active'], 'switch' ),
            'post_type_id' => $this->sanitize( $data['post_type_id'], 'number' ),
            'fb_post_id' => $this->sanitize( $data['fb_post_id'], 'text' ),
            'image_url' => $this->sanitize( $data['image_url'], 'text' ),
            'created_by'  => $this->sanitize( $data['created_by'], 'number' ),
            'created_at'  => $this->sanitize( $data['created_at'], 'text' ),
            'updated_at'  => $this->sanitize( $data['updated_at'], 'text' ),
        ];
    }

    /**
     * Posts item to a formatted array.
     *
     * @since 0.3.0
     *
     * @param object $post
     *
     * @return array
     */
    public static function to_array( ?object $post ): array {
        $post_type = static::get_post_type( $post );
        $fb_post_link = static::get_fb_post_link( $post );

        $data = [
            'id'          => (int) $post->id,
            'title'       => $post->title,
            'slug'        => $post->slug,
            'post_type'    => $post_type,
            'fb_post_id'   => $post->fb_post_id,
            'image_url'    => $post->image_url,
            'fb_post_link'   => $fb_post_link,
            'status'      => PostStatus::get_status_by_post( $post ),
            'company'     => static::get_post_company( $post ),
            'description' => $post->description,
            'created_at'  => $post->created_at,
            'updated_at'  => $post->updated_at,
        ];

        return $data;
    }

    /**
     * Get post type of a post.
     *
     * @since 0.3.0
     *
     * @param object $post
     *
     * @return object|null
     */
    public static function get_post_type( ?object $post ): ?object {
        $post_type = new PostType();

        $columns = 'id, name, slug';
        return $post_type->get( (int) $post->post_type_id, $columns );
    }

    /**
     * Get facebook post link of a post.
     *
     * @since 0.3.0
     *
     * @param object $post
     *
     * @return string|null
     */
    public static function get_fb_post_link( ?object $post ): ?string {

        if (empty($post->fb_post_id)) {
            return '';
        }
        
        return 'https://www.facebook.com/'.$post->fb_post_id;
    }

    /**
     * Get if post is a remote post or not.
     *
     * We'll fetch this from post_type_id.
     * If post type is for remote, then it's a remote post.
     *
     * @param object $post_type
     * @return boolean
     */
    public static function get_is_remote( ?object $post_type ): bool {
        if ( empty( $post_type ) ) {
            return false;
        }

        return $post_type->slug === 'remote';
    }

    /**
     * Get company of a post.
     *
     * @since 0.3.0
     *
     * @param object $post
     *
     * @return null | array
     */
    public static function get_post_company( ?object $post ): ?array {
        if ( empty( $post->company_id ) ) {
            return null;
        }

        /*$user = get_user_by( 'id', $post->company_id );

        if ( empty( $user ) ) {
            return null;
        }*/

        $socials = [
            '1' => [
                'id' => '1',
                'name' => 'Facebook',
                'avatar_url' => KEETECH_ADS_ASSETS . '/images/facebook.png',
            ],
            '2' => [
                'id' => '2',
                'name' => 'Twitter',
                'avatar_url' => KEETECH_ADS_ASSETS . '/images/twitter.png',
            ]
        ];

        return $socials[$post->company_id];
    }
}
