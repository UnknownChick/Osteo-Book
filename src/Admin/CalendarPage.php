<?php

declare(strict_types=1);

namespace OsteoBook\Admin;

use OsteoBook\Container;

class CalendarPage {

    public function __construct(
        private readonly Container $container
    ) {}

    public function render(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Accès refusé.', 'osteo-book' ) );
        }

        include OSTEOBOOK_PATH . 'resources/views/calendar.php';
    }
}
