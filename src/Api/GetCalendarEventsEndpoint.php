<?php

declare(strict_types=1);

namespace OsteoBook\Api;

use OsteoBook\Container;
use OsteoBook\Services\CalendarService;
use WP_REST_Request;
use WP_REST_Response;

class GetCalendarEventsEndpoint {

	public function __construct(
		private readonly Container $container
	) {}

	public function register( string $namespace ): void {
		register_rest_route( $namespace, '/calendar/events', [
			'methods' => 'GET',
			'callback' => [ $this, 'handle' ],
			'permission_callback' => fn () => current_user_can( 'manage_options' ),
			'args' => [
				'start' => [ 'required' => true, 'type' => 'string' ],
				'end' => [ 'required' => true, 'type' => 'string' ],
			],
		] );
	}

	public function handle( WP_REST_Request $request ): WP_REST_Response {
		$startStr = sanitize_text_field( (string) $request->get_param( 'start' ) );
		$endStr = sanitize_text_field( (string) $request->get_param( 'end' ) );

		try {
			$from = new \DateTime( $startStr );
			$to   = new \DateTime( $endStr );
		} catch ( \Exception ) {
			return new WP_REST_Response( [ 'error' => 'Invalid date range' ], 400 );
		}

		/** @var CalendarService $calendarService */
		$calendarService = $this->container->make( 'calendar.service' );

		return new WP_REST_Response( $calendarService->getEvents( $from, $to ), 200 );
	}
}
