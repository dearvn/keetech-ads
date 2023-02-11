<?php

namespace WpKeetech\KeetechAds\Posts;

class Manager {

    /**
     * Post class.
     *
     * @var Post
     */
    public $post;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->post = new Post();
    }

    /**
     * Get all posts by criteria.
     *
     * @since 0.3.0
     * @since 0.3.1 Fixed counting return type as integer.
     *
     * @param array $args
     * @return array|object|string|int
     */
    public function all( array $args = [] ) {
        $defaults = [
            'page'     => 1,
            'per_page' => 10,
            'orderby'  => 'id',
            'order'    => 'DESC',
            'search'   => '',
            'count'    => false,
            'where'    => [],
        ];

        $args = wp_parse_args( $args, $defaults );

        if ( ! empty( $args['search'] ) ) {
            global $wpdb;
            $like = '%' . $wpdb->esc_like( sanitize_text_field( wp_unslash( $args['search'] ) ) ) . '%';
            $args['where'][] = $wpdb->prepare( ' title LIKE %s OR description LIKE %s ', $like, $like );
        }

        if ( ! empty( $args['where'] ) ) {
            $args['where'] = ' WHERE ' . implode( ' AND ', $args['where'] );
        } else {
            $args['where'] = '';
        }

        $posts = $this->post->all( $args );

        if ( $args['count'] ) {
            return (int) $posts;
        }

        return $posts;
    }

    /**
     * Get single post by id|slug.
     *
     * @since 0.3.0
     *
     * @param array $args
     * @return array|object|null
     */
    public function get( array $args = [] ) {
        $defaults = [
            'key' => 'id',
            'value' => '',
        ];

        $args = wp_parse_args( $args, $defaults );

        if ( empty( $args['value'] ) ) {
            return null;
        }

        return $this->post->get_by( $args['key'], $args['value'] );
    }

    /**
     * Create a new post.
     *
     * @since 0.3.0
     *
     * @param array $data
     *
     * @return int | WP_Error $id
     */
    public function create( $data ) {
        // Prepare post data for database-insertion.
        $post_data = $this->post->prepare_for_database( $data );

        // Create post now.
        $post_id = $this->post->create(
            $post_data,
            [
                '%s',
                '%s',
                '%s',
                '%d',
                '%d',
                '%d',
                '%s',
                '%s',
                '%s',
                '%s',
            ]
        );

        if ( ! $post_id ) {
            return new \WP_Error( 'keetech_post_create_failed', __( 'Failed to create post.', 'keetech' ) );
        }

        /**
         * Fires after a post has been created.
         *
         * @since 0.3.0
         *
         * @param int   $post_id
         * @param array $post_data
         */
        do_action( 'keetech_posts_created', $post_id, $post_data );

        return $post_id;
    }

    /**
     * Update post.
     *
     * @since 0.3.0
     *
     * @param array $data
     * @param int   $post_id
     *
     * @return int | WP_Error $id
     */
    public function update( array $data, int $post_id ) {
        // Prepare post data for database-insertion.
        $post_data = $this->post->prepare_for_database( $data );

        // Update post.
        $updated = $this->post->update(
            $post_data,
            [
                'id' => $post_id,
            ],
            [
                '%s',
                '%s',
                '%s',
                '%d',
                '%d',
                '%d',
                '%d',
                '%s',
                '%s',
                '%s',
            ],
            [
                '%d',
            ]
        );

        if ( ! $updated ) {
            return new \WP_Error( 'keetech_post_update_failed', __( 'Failed to update post.', 'keetech' ) );
        }

        if ( $updated >= 0 ) {
            /**
             * Fires after a post is being updated.
             *
             * @since 0.3.0
             *
             * @param int   $post_id
             * @param array $post_data
             */
            do_action( 'keetech_posts_updated', $post_id, $post_data );

            return $post_id;
        }

        return new \WP_Error( 'keetech_post_update_failed', __( 'Failed to update the post.', 'keetech' ) );
    }

    /**
     * Delete posts data.
     *
     * @since 0.3.0
     *
     * @param array|int $post_ids
     *
     * @return int|WP_Error
     */
    public function delete( $post_ids ) {
        if ( is_array( $post_ids ) ) {
            $post_ids = array_map( 'absint', $post_ids );
        } else {
            $post_ids = [ absint( $post_ids ) ];
        }

        try {
            $this->post->query( 'START TRANSACTION' );

            $total_deleted = 0;
            foreach ( $post_ids as $post_id ) {
                $deleted = $this->post->delete(
                    [
                        'id' => $post_id,
                    ],
                    [
                        '%d',
                    ]
                );

                if ( $deleted ) {
                    $total_deleted += intval( $deleted );
                }

                /**
                 * Fires after a post has been deleted.
                 *
                 * @since 0.3.0
                 *
                 * @param int $post_id
                 */
                do_action( 'keetech_post_deleted', $post_id );
            }

            $this->post->query( 'COMMIT' );

            return $total_deleted;
        } catch ( \Exception $e ) {
            $this->post->query( 'ROLLBACK' );

            return new \WP_Error( 'keetech-post-delete-error', $e->getMessage() );
        }
    }

    /**
     * Publish post data to fanpage.
     *
     * @since 0.3.0
     *
     * @param array|int $params
     *
     * @return int|WP_Error
     */
    public function publishFacebook($data) {
        try {
                
            $settings = keetech_ads()->settings->get();

            $page_access_token = !empty($settings['fb_token']) ? $settings['fb_token'] : '';
            $page_id = !empty($settings['page_id']) ? $settings['page_id'] : '';

            if (empty($page_access_token) || empty($page_id)) {
                return;
            }
            
            //$page_access_token = 'EAAMnZCGbtbEkBAAYIAg9XypryZA2sV69aF7RPef0w8nM0NSWdeWHbabFVDYnvBzKUh2kdvpAeAFTPyO5FZC42Bc9zAJZCdY8gE4ZADRXsAgvCGMQovKToHbvU3lJzXCdMZCZCJZChjLyCTc6cTI0lAfH9emawUVw5kY7lhgbUGOc2agHGCr5GHRV';
            //$page_id = '278979152584771';    
         
            $body = [];
            $body['message'] = $data['description'];
            $body['caption'] = $data['title'];
            $body['description'] = $data['description'];
            $body['access_token'] = $page_access_token;

            $uri = '/feed';
            if (!empty($data['image_url'])) {
                $uri = '/photos';
                $body['url'] = $data['image_url'];
            } else if (!empty($data['video_url'])) {
                $uri = '/videos';
                $body['source'] = $data['video_url'];
                $body['title'] = $data['title'];
            }

            $post_url = 'https://graph.facebook.com/'.$page_id.$uri;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $post_url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $reps = curl_exec($ch);
            curl_close($ch);

            if (empty($reps)) {
                return '';
            }
            $item = json_decode($reps);

            $id = !empty($item->id) ? $item->id : '';
            
            if (!empty($data['image_url'])) {
                $id = !empty($item->post_id) ? $item->post_id : '';
            }
            
            return $id;
        } catch ( \Exception $e ) {
            echo $e->getMessage();

            return new \WP_Error( 'keetech-post-delete-error', $e->getMessage() );
        }
    }
}
