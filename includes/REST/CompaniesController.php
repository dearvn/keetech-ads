<?php

namespace WpKeetech\KeetechAds\REST;

use WpKeetech\KeetechAds\Abstracts\RESTController;
use WP_User_Query;
use WP_REST_Response;
use WP_REST_Server;
use WP_Error;

/**
 * API CompaniesController class.
 *
 * @since 0.5.0
 */
class CompaniesController extends RESTController {

    /**
     * Route base.
     *
     * @var string
     */
    protected $base = 'companies';

    /**
     * Register all routes related with carts.
     *
     * @return void
     */
    public function register_routes() {
        register_rest_route(
            $this->namespace, '/' . $this->base . '/dropdown//',
            [
                [
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => [ $this, 'get_items_dropdown' ],
                    'permission_callback' => [ $this, 'check_permission' ],
                ],
            ]
        );
    }

    /**
     * Retrieves a collection of companies for dropdown.
     *
     * @since 0.5.0
     *
     * @param WP_REST_Request $request   Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function get_items_dropdown( $request ): ?WP_REST_Response {
        $socials = [
            [
                'id' => '1',
                'name' => 'Facebook'
            ],
            [
                'id' => '2',
                'name' => 'Twitter'
            ],
        ];

        return rest_ensure_response( $socials );
    }

    /**
     * Prepare dropdown response for collection.
     *
     * @since 0.5.0
     *
     * @param WP_User         $item    User object.
	 * @param WP_REST_Request $request Request object.
     *
     * @return array
     */
    public function prepare_dropdown_response_for_collection( $item, $request ) {
        $user             = $item;
        $data             = [];
        $data['id']       = $user->id;
        $data['name']     = $user->name;

        return $data;
    }
}
