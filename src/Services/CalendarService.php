<?php

declare(strict_types=1);

namespace OsteoBook\Services;

use OsteoBook\Availability\AvailabilityManager;
use OsteoBook\Reservation\ReservationRepository;

class CalendarService {

    public function __construct(
        private readonly AvailabilityManager   $availabilityManager,
        private readonly ReservationRepository $reservationRepository,
    ) {}

    /** @return array<int, array<string, mixed>> FullCalendar event objects */
    public function getEvents( \DateTime $from, \DateTime $to ): array {
        $events = [];

        // Availability slots (green)
        $period = new \DatePeriod( $from, new \DateInterval( 'P1D' ), $to );
        foreach ( $period as $day ) {
            $date  = $day->format( 'Y-m-d' );
            $slots = $this->availabilityManager->getSlotsForDate( $date );

            foreach ( $slots as $slot ) {
                $events[] = [
                    'id' => 'avail_' . $date . '_' . str_replace( ':', '', $slot ),
                    'title' => __( 'Disponible', 'osteo-book' ),
                    'start' => $date . 'T' . $slot . ':00',
                    'backgroundColor' => '#10b981',
                    'borderColor' => '#10b981',
                    'textColor' => '#ffffff',
                    'extendedProps' => [ 'type' => 'availability' ],
                ];
            }
        }

        // Reservations (red, overrides available slot display)
        $reservations  = $this->reservationRepository->getAllForCalendar( $from, $to );
        $bookedEventIds = [];

        foreach ( $reservations as $r ) {
            $availId = 'avail_' . $r['date'] . '_' . str_replace( ':', '', $r['slot'] );
            $bookedEventIds[] = $availId;

            $events[] = [
                'id' => 'resa_' . $r['id'],
                'title' => sprintf( '%s %s – %s', $r['first_name'], $r['last_name'], $r['animal_name'] ),
                'start' => $r['date'] . 'T' . $r['slot'] . ':00',
                'backgroundColor' => '#ef4444',
                'borderColor' => '#ef4444',
                'textColor' => '#ffffff',
                'url' => get_edit_post_link( $r['id'], 'raw' ) ?: '',
                'extendedProps' => [ 'type' => 'reservation', 'status' => $r['status'] ],
            ];
        }

        // Remove availability events that are now booked
        $events = array_filter(
            $events,
            fn ( $e ) => ! in_array( $e['id'], $bookedEventIds, true )
        );

        usort( $events, fn ( $a, $b ) => strcmp( $a['start'], $b['start'] ) );

        return array_values( $events );
    }
}
