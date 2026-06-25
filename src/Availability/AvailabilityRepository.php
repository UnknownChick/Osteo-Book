<?php

declare(strict_types=1);

namespace OsteoBook\Availability;

class AvailabilityRepository {

    private const OPTION_KEY = 'osteobook_availability';

    public function getAll(): array {
        $defaults = [ 'weekly' => [], 'exceptions' => [] ];
        $value    = get_option( self::OPTION_KEY, $defaults );

        return is_array( $value ) ? $value : $defaults;
    }

    public function save( array $data ): bool {
        return (bool) update_option( self::OPTION_KEY, $data );
    }

    public function getWeeklySlots(): array {
        return $this->getAll()['weekly'] ?? [];
    }

    public function getExceptions(): array {
        return $this->getAll()['exceptions'] ?? [];
    }

    public function saveWeeklySlots( array $slots ): bool {
        $data             = $this->getAll();
        $data['weekly']   = $slots;

        return $this->save( $data );
    }

    public function saveException( string $date, array $slots ): bool {
        $data                        = $this->getAll();
        $data['exceptions'][ $date ] = $slots;

        return $this->save( $data );
    }

    public function removeException( string $date ): bool {
        $data = $this->getAll();
        unset( $data['exceptions'][ $date ] );

        return $this->save( $data );
    }
}
