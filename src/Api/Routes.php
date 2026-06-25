<?php

declare(strict_types=1);

namespace OsteoBook\Api;

use OsteoBook\Container;

class Routes {

	public const NAMESPACE = 'osteo-book/v1';

	public function __construct(
		private readonly Container $container
	) {}

	public function register(): void {
		( new GetSlotsEndpoint( $this->container ) )->register( self::NAMESPACE );
		( new CreateReservationEndpoint( $this->container ) )->register( self::NAMESPACE );
		( new GetCalendarEventsEndpoint( $this->container ) )->register( self::NAMESPACE );
		( new SaveAvailabilityEndpoint( $this->container ) )->register( self::NAMESPACE );
	}
}
