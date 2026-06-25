<?php

declare(strict_types=1);

namespace OsteoBook\Admin;

use OsteoBook\Availability\AvailabilityManager;
use OsteoBook\Container;

class SettingsPage {

    public function __construct(
        private readonly Container $container
    ) {}

    public function render(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Accès refusé.', 'osteo-book' ) );
        }

        /** @var AvailabilityManager $manager */
        $manager     = $this->container->make( 'availability.manager' );
        $weeklySlots = $manager->getWeeklySlots();
        $exceptions  = $manager->getExceptions();

        include OSTEOBOOK_PATH . 'resources/views/settings.php';
    }
}
