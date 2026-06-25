<?php

declare(strict_types=1);

namespace OsteoBook\Reservation;

use OsteoBook\DTO\ReservationDTO;

class ReservationRepository {

	private const POST_TYPE = 'reservation';

	public function create( ReservationDTO $dto ): int|\WP_Error {
		$postId = wp_insert_post( [
			'post_type' => self::POST_TYPE,
			'post_title' => sprintf(
				'%s %s – %s %s',
				$dto->firstName,
				$dto->lastName,
				$dto->date,
				$dto->slot
			),
			'post_status' => 'publish',
		], true );

		if ( is_wp_error( $postId ) ) {
			return $postId;
		}

		$meta = [
			'osteobook_first_name' => $dto->firstName,
			'osteobook_last_name' => $dto->lastName,
			'osteobook_email' => $dto->email,
			'osteobook_phone'   => $dto->phone,
			'osteobook_address' => $dto->address,
			'osteobook_animal_name' => $dto->animalName,
			'osteobook_animal_type' => $dto->animalType,
			'osteobook_date' => $dto->date,
			'osteobook_slot' => $dto->slot,
			'osteobook_message' => $dto->message,
			'osteobook_status' => 'confirmed',
		];

		foreach ( $meta as $key => $value ) {
			update_post_meta( $postId, $key, $value );
		}

		return $postId;
	}

	public function isSlotBooked( string $date, string $slot ): bool {
		$query = new \WP_Query( [
			'post_type' => self::POST_TYPE,
			'post_status' => 'publish',
			'posts_per_page' => 1,
			'fields' => 'ids',
			'no_found_rows' => true,
			'meta_query' => [
				'relation' => 'AND',
				[
					'key' => 'osteobook_date',
					'value' => $date,
					'compare' => '=',
				],
				[
					'key' => 'osteobook_slot',
					'value' => $slot,
					'compare' => '=',
				],
			],
		] );

		return count( $query->posts ) > 0;
	}

	/** @return string[] */
	public function getBookedSlotsForDate( string $date ): array {
		$query = new \WP_Query( [
			'post_type' => self::POST_TYPE,
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'fields' => 'ids',
			'no_found_rows' => true,
			'meta_query' => [
				[
					'key' => 'osteobook_date',
					'value' => $date,
					'compare' => '=',
				],
			],
		] );

		$slots = [];
		foreach ( $query->posts as $postId ) {
			$slot = get_post_meta( $postId, 'osteobook_slot', true );
			if ( $slot ) {
				$slots[] = (string) $slot;
			}
		}

		return $slots;
	}

	/** @return array<int, array<string, mixed>> */
	public function getAllForCalendar( \DateTime $from, \DateTime $to ): array {
		$query = new \WP_Query( [
			'post_type' => self::POST_TYPE,
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'fields' => 'ids',
			'no_found_rows' => true,
			'meta_query' => [
				'relation' => 'AND',
				[
					'key' => 'osteobook_date',
					'value' => $from->format( 'Y-m-d' ),
					'compare' => '>=',
					'type' => 'DATE',
				],
				[
					'key' => 'osteobook_date',
					'value' => $to->format( 'Y-m-d' ),
					'compare' => '<=',
					'type' => 'DATE',
				],
			],
		] );

		$reservations = [];
		foreach ( $query->posts as $postId ) {
			$reservations[] = [
				'id' => $postId,
				'date' => (string) get_post_meta( $postId, 'osteobook_date', true ),
				'slot' => (string) get_post_meta( $postId, 'osteobook_slot', true ),
				'first_name' => (string) get_post_meta( $postId, 'osteobook_first_name', true ),
				'last_name' => (string) get_post_meta( $postId, 'osteobook_last_name', true ),
				'animal_name' => (string) get_post_meta( $postId, 'osteobook_animal_name', true ),
				'status' => (string) get_post_meta( $postId, 'osteobook_status', true ),
			];
		}

		return $reservations;
	}
}
