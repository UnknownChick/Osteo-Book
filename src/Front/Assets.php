<?php

declare(strict_types=1);

namespace OsteoBook\Front;

class Assets {

    public function enqueueFront(): void {
        wp_enqueue_style(
            'osteobook-front',
            OSTEOBOOK_URL . 'resources/css/front.css',
            [],
            OSTEOBOOK_VERSION
        );

        wp_enqueue_script(
            'osteobook-front',
            OSTEOBOOK_URL . 'resources/js/front.js',
            [],
            OSTEOBOOK_VERSION,
            true
        );

        wp_localize_script( 'osteobook-front', 'osteoBookConfig', [
            'apiUrl' => rest_url( 'osteo-book/v1' ),
            'nonce' => wp_create_nonce( 'wp_rest' ),
            'i18n' => [
                'loading' => __( 'Chargement...', 'osteo-book' ),
                'noSlots' => __( 'Aucun créneau disponible pour cette date.', 'osteo-book' ),
                'submitting' => __( 'Envoi en cours...', 'osteo-book' ),
                'success' => __( 'Réservation confirmée !', 'osteo-book' ),
                'error' => __( 'Une erreur est survenue. Veuillez réessayer.', 'osteo-book' ),
            ],
        ] );
    }

    public function enqueueAdmin( string $hook ): void {
        if ( ! str_contains( $hook, 'osteobook' ) ) {
            return;
        }

        // FullCalendar (CDN)
        wp_enqueue_style(
            'fullcalendar',
            'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css',
            [],
            '6.1.15'
        );

        wp_enqueue_script(
            'fullcalendar',
            'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js',
            [],
            '6.1.15',
            true
        );

        wp_enqueue_script(
            'fullcalendar-fr',
            'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/locales/fr.global.min.js',
            [ 'fullcalendar' ],
            '6.1.15',
            true
        );

        wp_enqueue_style(
            'osteobook-admin',
            OSTEOBOOK_URL . 'resources/css/admin.css',
            [ 'fullcalendar' ],
            OSTEOBOOK_VERSION
        );

        wp_enqueue_script(
            'osteobook-admin',
            OSTEOBOOK_URL . 'resources/js/admin.js',
            [ 'fullcalendar' ],
            OSTEOBOOK_VERSION,
            true
        );

        wp_localize_script( 'osteobook-admin', 'osteoBookAdmin', [
            'apiUrl' => rest_url( 'osteo-book/v1' ),
            'nonce' => wp_create_nonce( 'wp_rest' ),
            'i18n' => [
                'confirmDelete' => __( 'Supprimer cette exception ?', 'osteo-book' ),
                'saved' => __( 'Enregistré avec succès.', 'osteo-book' ),
                'error' => __( 'Erreur lors de la sauvegarde.', 'osteo-book' ),
                'networkError' => __( 'Erreur réseau.', 'osteo-book' ),
            ],
        ] );
    }
}
