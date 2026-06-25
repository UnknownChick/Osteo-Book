<?php

declare(strict_types=1);

namespace OsteoBook\Admin;

use OsteoBook\Container;

class Menu {

    public function __construct(
        private readonly Container $container
    ) {}

    public function register(): void {
        $calendarPage = new CalendarPage( $this->container );
        $settingsPage = new SettingsPage( $this->container );

        add_menu_page(
            __( 'OsteoBook', 'osteo-book' ),
            __( 'OsteoBook', 'osteo-book' ),
            'manage_options',
            'osteobook',
            [ $calendarPage, 'render' ],
            'dashicons-calendar-alt',
            30
        );

        add_submenu_page(
            'osteobook',
            __( 'Calendrier', 'osteo-book' ),
            __( 'Calendrier', 'osteo-book' ),
            'manage_options',
            'osteobook',
            [ $calendarPage, 'render' ],
        );

        add_submenu_page(
            'osteobook',
            __( 'Disponibilités', 'osteo-book' ),
            __( 'Disponibilités', 'osteo-book' ),
            'manage_options',
            'osteobook-settings',
            [ $settingsPage, 'render' ],
        );
    }
}
