<?php

declare(strict_types=1);

namespace OsteoBook\DTO;

class ReservationDTO {

	public function __construct(
		public readonly string $firstName,
		public readonly string $lastName,
		public readonly string $email,
		public readonly string $phone,
		public readonly string $animalName,
		public readonly string $animalType,
		public readonly string $date,
		public readonly string $slot,
		public readonly string $message = '',
	) {}

	public static function fromArray( array $data ): self {
		return new self(
			firstName: sanitize_text_field( $data['first_name'] ?? '' ),
			lastName: sanitize_text_field( $data['last_name'] ?? '' ),
			email: sanitize_email( $data['email'] ?? '' ),
			phone: sanitize_text_field( $data['phone'] ?? '' ),
			animalName: sanitize_text_field( $data['animal_name'] ?? '' ),
			animalType: sanitize_text_field( $data['animal_type'] ?? '' ),
			date: sanitize_text_field( $data['date'] ?? '' ),
			slot: sanitize_text_field( $data['slot'] ?? '' ),
			message: sanitize_textarea_field( $data['message'] ?? '' ),
		);
	}
}
