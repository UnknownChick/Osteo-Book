<?php

declare(strict_types=1);

namespace OsteoBook\Reservation;

use OsteoBook\DTO\ReservationDTO;
use OsteoBook\Services\MailService;

class ReservationManager {

	public function __construct(
		private readonly ReservationRepository $repository,
		private readonly ReservationValidator  $validator,
		private readonly MailService           $mailService,
	) {}

	/**
	 * @return array{success: bool, data: array<string,mixed>|null, errors: string[]}
	 */
	public function create( ReservationDTO $dto ): array {
		$errors = $this->validator->validate( $dto );

		if ( ! empty( $errors ) ) {
			return [ 'success' => false, 'data' => null, 'errors' => $errors ];
		}

		$postId = $this->repository->create( $dto );

		if ( is_wp_error( $postId ) ) {
			return [
				'success' => false,
				'data' => null,
				'errors' => [ $postId->get_error_message() ],
			];
		}

		$this->mailService->notifyCustomer( $dto );
		$this->mailService->notifyAdmin( $dto );

		return [ 'success' => true, 'data' => [ 'id' => $postId ], 'errors' => [] ];
	}
}
