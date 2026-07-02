<?php

declare(strict_types=1);

namespace OsteoBook\Api;

use OsteoBook\Container;
use OsteoBook\DTO\ReservationDTO;
use OsteoBook\Reservation\ReservationManager;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

class CreateReservationEndpoint {

	public function __construct(
		private readonly Container $container
	) {}

	public function register( string $namespace ): void {
		register_rest_route( $namespace, '/reservations', [
			'methods' => WP_REST_Server::CREATABLE,
			'callback' => [ $this, 'handle' ],
			'permission_callback' => '__return_true',
		] );
	}

	public function handle( WP_REST_Request $request ): WP_REST_Response {
		$params = $request->get_json_params() ?? $request->get_body_params();

		if ( empty( $params ) || ! is_array( $params ) ) {
			return new WP_REST_Response( [
				'success' => false,
				'errors' => [ __( 'Données invalides.', 'osteo-book' ) ]
			], 400 );
		}

		$dto = ReservationDTO::fromArray( $params );

		/** @var ReservationManager $manager */
		$manager = $this->container->make( 'reservation.manager' );
		$result  = $manager->create( $dto );

		if ( ! $result['success'] ) {
			return new WP_REST_Response( [
				'success' => false,
				'errors' => $result['errors']
			], 422 );
		}

		return new WP_REST_Response( [
			'success' => true,
			'data' => $result['data'],
			'message' => __( 'Réservation confirmée ! Vous allez recevoir un email de confirmation.', 'osteo-book' ),
		], 201 );
	}
}
