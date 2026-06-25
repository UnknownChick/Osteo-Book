<?php

declare(strict_types=1);

namespace OsteoBook\Reservation;

use OsteoBook\Availability\AvailabilityManager;
use OsteoBook\DTO\ReservationDTO;

class ReservationValidator {

	public function __construct(
		private readonly AvailabilityManager   $availabilityManager,
		private readonly ReservationRepository $reservationRepository,
	) {}

	/** @return string[]  Empty array = valid */
	public function validate( ReservationDTO $dto ): array {
		$errors = [];

		if ( $dto->firstName === '' ) {
			$errors[] = __( 'Le prénom est requis.', 'osteo-book' );
		}
		if ( $dto->lastName === '' ) {
			$errors[] = __( 'Le nom est requis.', 'osteo-book' );
		}
		if ( $dto->email === '' || ! is_email( $dto->email ) ) {
			$errors[] = __( "L'adresse email est invalide.", 'osteo-book' );
		}
		if ( $dto->phone === '' ) {
			$errors[] = __( 'Le numéro de téléphone est requis.', 'osteo-book' );
		}
		if ( $dto->address === '' ) {
			$errors[] = __( "L'adresse du lieu de rendez-vous est requise.", 'osteo-book' );
		}
		if ( $dto->animalName === '' ) {
			$errors[] = __( "Le nom de l'animal est requis.", 'osteo-book' );
		}
		if ( ! $this->isValidDate( $dto->date ) ) {
			$errors[] = __( 'La date est invalide ou passée.', 'osteo-book' );
		}
		if ( ! preg_match( '/^\d{2}:\d{2}$/', $dto->slot ) ) {
			$errors[] = __( 'Le créneau est invalide.', 'osteo-book' );
		}

		// Stop here if basic fields are wrong
		if ( ! empty( $errors ) ) {
			return $errors;
		}

		$available = $this->availabilityManager->getSlotsForDate( $dto->date );

		if ( ! in_array( $dto->slot, $available, true ) ) {
			$errors[] = __( "Ce créneau n'est pas disponible pour cette date.", 'osteo-book' );

			return $errors;
		}

		if ( $this->reservationRepository->isSlotBooked( $dto->date, $dto->slot ) ) {
			$errors[] = __( 'Ce créneau est déjà réservé.', 'osteo-book' );
		}

		return $errors;
	}

	private function isValidDate( string $date ): bool {
		$d = \DateTime::createFromFormat( 'Y-m-d', $date );

		return $d instanceof \DateTime
			&& $d->format( 'Y-m-d' ) === $date
			&& $d >= new \DateTime( 'today' );
	}
}
