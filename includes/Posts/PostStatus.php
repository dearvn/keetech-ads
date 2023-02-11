<?php

namespace WpKeetech\KeetechAds\Posts;

/**
 * PostStatus class.
 *
 * @since 0.3.0
 */
class PostStatus {

    /**
     * Draft status.
     *
     * @since 0.3.0
     */
    const DRAFT = 'draft';

    /**
     * Published status.
     *
     * @since 0.3.0
     */
    const PUBLISHED = 'published';

    /**
     * Trashed status.
     *
     * @since 0.3.0
     */
    const TRASHED = 'trashed';

    /**
     * Get post status.
     *
     * @since 0.3.0
     *
     * @param object $post
     */
    public static function get_status_by_post( object $post ): string {
        if ( ! empty( $post->deleted_at ) ) {
            return self::TRASHED;
        }

        if ( $post->is_active ) {
            return self::PUBLISHED;
        }

        return self::DRAFT;
    }
}
