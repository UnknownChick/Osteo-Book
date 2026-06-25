<?php

declare(strict_types=1);

namespace OsteoBook\Availability;

class AvailabilityManager {

	private const DAY_MAP = [
		0 => 'sunday',
		1 => 'monday',
		2 => 'tuesday',
		3 => 'wednesday',
		4 => 'thursday',
		5 => 'friday',
		6 => 'saturday',
	];

	public function __construct(
		private readonly AvailabilityRepository $repository
	) {}

	/**
	 * Returns the available slots for a given date.
	 * Exceptions (specific dates) override weekly defaults.
	 */
	public function getSlotsForDate( string $date ): array {
		$data = $this->repository->getAll();
		$exceptions = $data['exceptions'] ?? [];

		if ( array_key_exists( $date, $exceptions ) ) {
			return $exceptions[ $date ];
		}

		$dayOfWeek = (int) ( new \DateTime( $date ) )->format( 'w' );
		$dayName = self::DAY_MAP[ $dayOfWeek ];

		return $data['weekly'][ $dayName ] ?? [];
	}

	public function getWeeklySlots(): array {
		return $this->repository->getWeeklySlots();
	}

	public function getExceptions(): array {
		return $this->repository->getExceptions();
	}

	/**
	 * @param array<string, string[]> $slots  e.g. ['monday' => ['09:00', '10:00']]
	 */
	public function saveWeeklySlots( array $slots ): bool {
		$clean = [];

		foreach ( $slots as $day => $daySlots ) {
			if ( ! in_array( $day, self::DAY_MAP, true ) ) {
				continue;
			}
			$valid = [];
			foreach ( (array) $daySlots as $s ) {
				if ( preg_match( '/^\d{2}:\d{2}$/', (string) $s ) ) {
					$valid[] = $s;
				}
			}
			$valid = array_unique( $valid );
			sort( $valid );
			$clean[ $day ] = array_values( $valid );
		}

		return $this->repository->saveWeeklySlots( $clean );
	}

	public function saveException( string $date, array $slots ): bool {
		if ( ! $this->isValidDate( $date ) ) {
			return false;
		}

		$clean = [];
		foreach ( $slots as $s ) {
			if ( preg_match( '/^\d{2}:\d{2}$/', (string) $s ) ) {
				$clean[] = $s;
			}
		}
		sort( $clean );

		return $this->repository->saveException( $date, array_values( $clean ) );
	}

	public function removeException( string $date ): bool {
		return $this->repository->removeException( $date );
	}

	/** @return array<int,string> */
	public function getDayMap(): array {
		return self::DAY_MAP;
	}

	private function isValidDate( string $date ): bool {
		$d = \DateTime::createFromFormat( 'Y-m-d', $date );

		return $d instanceof \DateTime && $d->format( 'Y-m-d' ) === $date;
	}
}
