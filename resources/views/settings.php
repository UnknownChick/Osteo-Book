<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<div class="wrap osteobook-admin-wrap">
    <h1><?php esc_html_e( 'OsteoBook – Disponibilités', 'osteo-book' ); ?></h1>

    <div class="osteobook-settings">

        <!-- ── Créneaux hebdomadaires ── -->
        <div class="osteobook-card">
            <h2><?php esc_html_e( 'Créneaux hebdomadaires', 'osteo-book' ); ?></h2>
            <p class="description">
                <?php esc_html_e( 'Définissez les créneaux disponibles par jour. Ces créneaux se répètent chaque semaine.', 'osteo-book' ); ?>
            </p>

            <form id="osteobook-weekly-form">
                <?php wp_nonce_field( 'osteobook_weekly', 'osteobook_nonce' ); ?>
                <table class="widefat osteobook-days-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( 'Jour', 'osteo-book' ); ?></th>
                            <th><?php esc_html_e( 'Créneaux (HH:MM séparés par des espaces)', 'osteo-book' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $dayLabels = [
                            'monday'    => __( 'Lundi',      'osteo-book' ),
                            'tuesday'   => __( 'Mardi',      'osteo-book' ),
                            'wednesday' => __( 'Mercredi',   'osteo-book' ),
                            'thursday'  => __( 'Jeudi',      'osteo-book' ),
                            'friday'    => __( 'Vendredi',   'osteo-book' ),
                            'saturday'  => __( 'Samedi',     'osteo-book' ),
                            'sunday'    => __( 'Dimanche',   'osteo-book' ),
                        ];
                        foreach ( $dayLabels as $dayKey => $dayLabel ) :
                            $slots = implode( ' ', $weeklySlots[ $dayKey ] ?? [] );
                        ?>
                        <tr>
                            <td><strong><?php echo esc_html( $dayLabel ); ?></strong></td>
                            <td>
                                <input type="text"
                                       name="days[<?php echo esc_attr( $dayKey ); ?>]"
                                       value="<?php echo esc_attr( $slots ); ?>"
                                       placeholder="09:00 10:00 11:00 14:00"
                                       class="large-text">
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <p class="submit">
                    <button type="submit" class="button button-primary">
                        <?php esc_html_e( 'Enregistrer les créneaux', 'osteo-book' ); ?>
                    </button>
                </p>
            </form>
        </div>

        <!-- ── Exceptions ── -->
        <div class="osteobook-card">
            <h2><?php esc_html_e( 'Exceptions (dates spécifiques)', 'osteo-book' ); ?></h2>
            <p class="description">
                <?php esc_html_e( "Remplacez les créneaux d'un jour précis. Laisser vide = fermé ce jour-là.", 'osteo-book' ); ?>
            </p>

            <div id="osteobook-exception-list">
                <?php if ( empty( $exceptions ) ) : ?>
                    <p class="osteobook-empty"><?php esc_html_e( 'Aucune exception définie.', 'osteo-book' ); ?></p>
                <?php else : ?>
                    <table class="widefat striped">
                        <thead>
                            <tr>
                                <th><?php esc_html_e( 'Date', 'osteo-book' ); ?></th>
                                <th><?php esc_html_e( 'Créneaux', 'osteo-book' ); ?></th>
                                <th><?php esc_html_e( 'Actions', 'osteo-book' ); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ( $exceptions as $exDate => $exSlots ) : ?>
                            <tr data-date="<?php echo esc_attr( (string) $exDate ); ?>">
                                <td><?php echo esc_html( (string) $exDate ); ?></td>
                                <td><?php echo esc_html( empty( $exSlots ) ? '—' : implode( ', ', $exSlots ) ); ?></td>
                                <td>
                                    <button type="button"
                                            class="button osteobook-delete-exception"
                                            data-date="<?php echo esc_attr( (string) $exDate ); ?>">
                                        <?php esc_html_e( 'Supprimer', 'osteo-book' ); ?>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <form id="osteobook-exception-form" style="margin-top:24px;padding-top:16px;border-top:1px solid #e5e7eb">
                <h3><?php esc_html_e( 'Ajouter une exception', 'osteo-book' ); ?></h3>
                <div class="osteobook-row">
                    <div class="osteobook-field">
                        <label for="osteobook-ex-date"><?php esc_html_e( 'Date', 'osteo-book' ); ?></label>
                        <input type="date" id="osteobook-ex-date" name="date" required class="regular-text">
                    </div>
                    <div class="osteobook-field">
                        <label for="osteobook-ex-slots">
                            <?php esc_html_e( 'Créneaux (laisser vide = fermé)', 'osteo-book' ); ?>
                        </label>
                        <input type="text" id="osteobook-ex-slots" name="slots"
                               placeholder="09:00 10:00" class="regular-text">
                    </div>
                </div>
                <button type="submit" class="button button-primary">
                    <?php esc_html_e( 'Ajouter', 'osteo-book' ); ?>
                </button>
            </form>
        </div>

    </div><!-- /.osteobook-settings -->
</div><!-- /.wrap -->
