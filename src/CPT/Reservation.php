<?php

declare(strict_types=1);

namespace OsteoBook\CPT;

class Reservation {

    public function register(): void {
        register_extended_post_type(
            'reservation',
            [
                'show_ui' => true,
                'show_in_menu' => 'osteobook',
                'supports' => [ 'title' ],
                'show_in_rest' => false,
                'admin_cols' => [
                    'date_resa' => [
                        'title' => __( 'Date', 'osteo-book' ),
                        'meta_key' => 'osteobook_date',
                    ],
                    'slot_resa' => [
                        'title' => __( 'Créneau', 'osteo-book' ),
                        'meta_key' => 'osteobook_slot',
                    ],
                    'animal_resa' => [
                        'title' => __( 'Animal', 'osteo-book' ),
                        'meta_key' => 'osteobook_animal_name',
                    ],
                    'owner_resa'  => [
                        'title' => __( 'Propriétaire', 'osteo-book' ),
                        'function' => function ( $post ) {
                            printf(
                                '%s %s',
                                esc_html( (string) get_post_meta( $post->ID, 'osteobook_first_name', true ) ),
                                esc_html( (string) get_post_meta( $post->ID, 'osteobook_last_name', true ) )
                            );
                        },
                    ],
                    'status_resa' => [
                        'title'    => __( 'Statut', 'osteo-book' ),
                        'meta_key' => 'osteobook_status',
                    ],
                ],
                'admin_filters' => [
                    'status_resa' => [
                        'title' => __( 'Statut', 'osteo-book' ),
                        'meta_key' => 'osteobook_status',
                    ],
                ],
            ],
            [
                'singular' => __( 'Réservation', 'osteo-book' ),
                'plural' => __( 'Réservations', 'osteo-book' ),
                'slug' => 'reservations',
            ]
        );
    }
}
