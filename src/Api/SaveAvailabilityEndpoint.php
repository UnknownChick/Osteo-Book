<?php

declare(strict_types=1);

namespace OsteoBook\Api;

use OsteoBook\Availability\AvailabilityManager;
use OsteoBook\Container;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

class SaveAvailabilityEndpoint {

	public function __construct(
		private readonly Container $container
	) {}

	public function register( string $namespace ): void {
		register_rest_route( $namespace, '/availability', [
			'methods' => WP_REST_Server::CREATABLE,
			'callback' => [ $this, 'saveWeekly' ],
			'permission_callback' => fn () => current_user_can( 'manage_options' ),
		] );

		register_rest_route( $namespace, '/availability/exception', [
			[
				'methods' => WP_REST_Server::CREATABLE,
				'callback' => [ $this, 'saveException' ],
				'permission_callback' => fn () => current_user_can( 'manage_options' ),
			],
			[
				'methods' => WP_REST_Server::DELETABLE,
				'callback' => [ $this, 'deleteException' ],
				'permission_callback' => fn () => current_user_can( 'manage_options' ),
			],
		] );
	}

	public function saveWeekly( WP_REST_Request $request ): WP_REST_Response {
		$params = $request->get_json_params();
		$slots  = $params['weekly'] ?? null;

		if ( ! is_array( $slots ) ) {
			return new WP_REST_Response( [ 'error' => 'Invalid data' ], 400 );
		}

		/** @var AvailabilityManager $manager */
		$manager = $this->container->make( 'availability.manager' );
		$manager->saveWeeklySlots( $slots );

		return new WP_REST_Response( [ 'success' => true ], 200 );
	}

	public function saveException( WP_REST_Request $request ): WP_REST_Response {
		$params = $request->get_json_params();
		$date   = sanitize_text_field( (string) ( $params['date']  ?? '' ) );
		$slots  = (array) ( $params['slots'] ?? [] );

		/** @var AvailabilityManager $manager */
		$manager = $this->container->make( 'availability.manager' );
		$result  = $manager->saveException( $date, $slots );

		return new WP_REST_Response( [ 'success' => $result ], $result ? 200 : 400 );
	}

	public function deleteException( WP_REST_Request $request ): WP_REST_Response {
		$params = $request->get_json_params();
		$date   = sanitize_text_field( (string) ( $params['date'] ?? '' ) );

		/** @var AvailabilityManager $manager */
		$manager = $this->container->make( 'availability.manager' );
		$manager->removeException( $date );

		return new WP_REST_Response( [ 'success' => true ], 200 );
	}
}
