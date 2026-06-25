<?php

declare(strict_types=1);

namespace OsteoBook\Api;

use OsteoBook\Availability\AvailabilityManager;
use OsteoBook\Container;
use OsteoBook\Reservation\ReservationRepository;
use WP_REST_Request;
use WP_REST_Response;

class GetSlotsEndpoint {

	public function __construct(
		private readonly Container $container
	) {}

	public function register( string $namespace ): void {
		register_rest_route( $namespace, '/slots', [
			'methods' => 'GET',
			'callback' => [ $this, 'handle' ],
			'permission_callback' => '__return_true',
			'args' => [
				'date' => [
					'required' => true,
					'type' => 'string',
					'validate_callback' => fn ( $value ) => (bool) preg_match( '/^\d{4}-\d{2}-\d{2}$/', (string) $value ),
					'sanitize_callback' => 'sanitize_text_field',
				],
			],
		] );
	}

	public function handle( WP_REST_Request $request ): WP_REST_Response {
		$date = (string) $request->get_param( 'date' );

		// Do not expose past dates
		if ( $date < ( new \DateTime( 'today' ) )->format( 'Y-m-d' ) ) {
			return new WP_REST_Response( [ 'date' => $date, 'slots' => [] ], 200 );
		}

		/** @var AvailabilityManager $availabilityManager */
		$availabilityManager = $this->container->make( 'availability.manager' );

		/** @var ReservationRepository $reservationRepository */
		$reservationRepository = $this->container->make( 'reservation.repository' );

		$all    = $availabilityManager->getSlotsForDate( $date );
		$booked = $reservationRepository->getBookedSlotsForDate( $date );

		return new WP_REST_Response( [
			'date'  => $date,
			'slots' => array_values( array_diff( $all, $booked ) ),
		], 200 );
	}
}
