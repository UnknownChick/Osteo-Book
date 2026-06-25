<?php

declare(strict_types=1);

namespace OsteoBook\CPT;

use Extended\ACF\Fields\Email;
use Extended\ACF\Fields\Select;
use Extended\ACF\Fields\Tab;
use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\Textarea;
use Extended\ACF\Location;

class ReservationFields {

    public function register(): void {
        if ( ! function_exists( 'register_extended_field_group' ) ) {
            return;
        }

        register_extended_field_group( [
            'title' => __( 'Détails de la réservation', 'osteo-book' ),
            'fields' => [

                // ── Rendez-vous ────────────────────────────────────────────
                Tab::make( __( 'Rendez-vous', 'osteo-book' ), 'osteobook_tab_appointment' ),

                Text::make( __( 'Date du RDV', 'osteo-book' ), 'osteobook_date' )
                    ->helperText( __( 'Format : YYYY-MM-DD', 'osteo-book' ) )
                    ->required(),

                Text::make( __( 'Créneau', 'osteo-book' ), 'osteobook_slot' )
                    ->helperText( __( 'Format : HH:MM (ex. 09:00)', 'osteo-book' ) )
                    ->required(),

                Select::make( __( 'Statut', 'osteo-book' ), 'osteobook_status' )
                    ->choices( [
                        'confirmed' => __( 'Confirmé',   'osteo-book' ),
                        'pending'   => __( 'En attente', 'osteo-book' ),
                        'cancelled' => __( 'Annulé',     'osteo-book' ),
                    ] )
                    ->default( 'confirmed' )
                    ->required(),

                // ── Propriétaire ───────────────────────────────────────────
                Tab::make( __( 'Propriétaire', 'osteo-book' ), 'osteobook_tab_owner' ),

                Text::make( __( 'Prénom', 'osteo-book' ), 'osteobook_first_name' )
                    ->required()
                    ->column( 50 ),

                Text::make( __( 'Nom', 'osteo-book' ), 'osteobook_last_name' )
                    ->required()
                    ->column( 50 ),

                Email::make( __( 'Email', 'osteo-book' ), 'osteobook_email' )
                    ->required()
                    ->column( 50 ),

                Text::make( __( 'Téléphone', 'osteo-book' ), 'osteobook_phone' )
                    ->required()
                    ->column( 50 ),

                // ── Animal ─────────────────────────────────────────────────
                Tab::make( __( 'Animal', 'osteo-book' ), 'osteobook_tab_animal' ),

                Text::make( __( "Nom de l'animal", 'osteo-book' ), 'osteobook_animal_name' )
                    ->required()
                    ->column( 50 ),

                Text::make( __( 'Espèce / Race', 'osteo-book' ), 'osteobook_animal_type' )
                    ->column( 50 ),

                Textarea::make( __( 'Message', 'osteo-book' ), 'osteobook_message' )
                    ->rows( 4 ),

            ],
            'location' => [
                Location::where( 'post_type', 'reservation' ),
            ],
        ] );
    }
}
