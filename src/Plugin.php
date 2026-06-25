<?php

declare(strict_types=1);

namespace OsteoBook;

use OsteoBook\Admin\Menu;
use OsteoBook\Api\Routes;
use OsteoBook\Availability\AvailabilityManager;
use OsteoBook\Availability\AvailabilityRepository;
use OsteoBook\CPT\Reservation;
use OsteoBook\CPT\ReservationFields;
use OsteoBook\Front\Assets;
use OsteoBook\Front\Shortcode;
use OsteoBook\Reservation\ReservationManager;
use OsteoBook\Reservation\ReservationRepository;
use OsteoBook\Reservation\ReservationValidator;
use OsteoBook\Services\CalendarService;
use OsteoBook\Services\MailService;

class Plugin {

    private Container $container;

    public function __construct() {
        $this->container = new Container();
        $this->bindServices();
    }

    private function bindServices(): void {
        $c = $this->container;

        $c->bind( 'availability.repository', fn () => new AvailabilityRepository() );
        $c->bind( 'availability.manager', fn () => new AvailabilityManager( $c->make( 'availability.repository' ) ) );
        $c->bind( 'reservation.repository', fn () => new ReservationRepository() );
        $c->bind( 'mail.service', fn () => new MailService() );
        $c->bind( 'reservation.validator', fn () => new ReservationValidator(
            $c->make( 'availability.manager' ),
            $c->make( 'reservation.repository' )
        ) );
        $c->bind( 'reservation.manager', fn () => new ReservationManager(
            $c->make( 'reservation.repository' ),
            $c->make( 'reservation.validator' ),
            $c->make( 'mail.service' )
        ) );
        $c->bind( 'calendar.service', fn () => new CalendarService(
            $c->make( 'availability.manager' ),
            $c->make( 'reservation.repository' )
        ) );
    }

    public function boot(): void {
        // Custom Post Type
        add_action( 'init', [ new Reservation(), 'register' ] );

        // ACF field group (requires SCF/ACF plugin)
        add_action( 'acf/include_fields', [ new ReservationFields(), 'register' ] );

        // Admin
        if ( is_admin() ) {
            $menu = new Menu( $this->container );
            add_action( 'admin_menu', [ $menu, 'register' ] );
        }

        // REST API
        $routes = new Routes( $this->container );
        add_action( 'rest_api_init', [ $routes, 'register' ] );

        // Shortcode
        $shortcode = new Shortcode( $this->container );
        add_shortcode( 'osteo_book', [ $shortcode, 'render' ] );

        // Assets
        $assets = new Assets();
        add_action( 'wp_enqueue_scripts',    [ $assets, 'enqueueFront' ] );
        add_action( 'admin_enqueue_scripts', [ $assets, 'enqueueAdmin' ] );
    }
}
