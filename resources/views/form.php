<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<div id="osteobook-form-wrap" class="osteobook-wrap">

    <div id="osteobook-success" class="osteobook-alert osteobook-alert--success alert alert--soft-green" style="display:none;" role="alert"></div>
    <div id="osteobook-errors"  class="osteobook-alert osteobook-alert--error alert alert--soft-red" style="display:none;" role="alert"></div>

    <form id="osteobook-form" class="osteobook-form" novalidate>

        <!-- Step 1 : Date + Créneau -->
        <div class="osteobook-step" data-step="1">

            <h3 class="osteobook-step__title">
                <?php esc_html_e( 'Étape 1 - Choisissez votre date et créneau', 'osteo-book' ); ?>
            </h3>

            <div class="osteobook-field input-container">
                <label for="osteobook-date">
                    <?php esc_html_e( 'Date', 'osteo-book' ); ?>
                </label>
                <input type="date"
                       id="osteobook-date"
                       name="date"
                       min="<?= esc_attr( gmdate( 'Y-m-d', strtotime( '+1 day' ) ) ); ?>"
                       required
                       class="osteobook-input">
            </div>

            <div class="osteobook-field input-container" id="osteobook-slots-wrap" style="display:none;">
                <label><?php esc_html_e( 'Créneau disponible', 'osteo-book' ); ?></label>
                <div id="osteobook-slots" class="osteobook-slots" role="group" aria-label="<?php esc_attr_e( 'Créneaux disponibles', 'osteo-book' ); ?>"></div>
                <input type="hidden" name="slot" id="osteobook-slot-input">
            </div>

        </div>

        <!-- Step 2 : Informations -->
        <div class="osteobook-step" data-step="2" style="display:none;">

            <h3 class="osteobook-step__title">
                <?php esc_html_e( 'Étape 2 – Vos informations', 'osteo-book' ); ?>
            </h3>

            <div class="osteobook-row">
                <div class="osteobook-field input-container">
                    <label for="osteobook-first-name"><?php esc_html_e( 'Prénom', 'osteo-book' ); ?></label>
                    <input type="text" id="osteobook-first-name" name="first_name" required
                           class="osteobook-input" autocomplete="given-name">
                </div>
                <div class="osteobook-field input-container">
                    <label for="osteobook-last-name"><?php esc_html_e( 'Nom', 'osteo-book' ); ?></label>
                    <input type="text" id="osteobook-last-name" name="last_name" required
                           class="osteobook-input" autocomplete="family-name">
                </div>
            </div>

            <div class="osteobook-row">
                <div class="osteobook-field input-container">
                    <label for="osteobook-email"><?php esc_html_e( 'Email', 'osteo-book' ); ?></label>
                    <input type="email" id="osteobook-email" name="email" required
                           class="osteobook-input" autocomplete="email">
                </div>
                <div class="osteobook-field input-container">
                    <label for="osteobook-phone"><?php esc_html_e( 'Téléphone', 'osteo-book' ); ?></label>
                    <input type="tel" id="osteobook-phone" name="phone" required
                           class="osteobook-input" autocomplete="tel">
                </div>
            </div>

            <h3 class="osteobook-step__title">
                <?php esc_html_e( 'Votre animal', 'osteo-book' ); ?>
            </h3>

            <div class="osteobook-row">
                <div class="osteobook-field input-container">
                    <label for="osteobook-animal-name"><?php esc_html_e( "Nom de l'animal", 'osteo-book' ); ?></label>
                    <input type="text" id="osteobook-animal-name" name="animal_name" required
                           class="osteobook-input">
                </div>
                <div class="osteobook-field input-container">
                    <label for="osteobook-animal-type"><?php esc_html_e( 'Espèce / Race', 'osteo-book' ); ?></label>
                    <input type="text" id="osteobook-animal-type" name="animal_type"
                           class="osteobook-input"
                           placeholder="<?php esc_attr_e( 'Cheval, Chien, Chat...', 'osteo-book' ); ?>">
                </div>
            </div>

            <div class="osteobook-field input-container">
                <label for="osteobook-message"><?php esc_html_e( 'Message (optionnel)', 'osteo-book' ); ?></label>
                <textarea id="osteobook-message" name="message" class="osteobook-input" rows="3"></textarea>
            </div>

            <!-- Summary -->
            <div class="osteobook-summary" id="osteobook-summary" aria-live="polite"></div>

        </div>

        <!-- Navigation -->
        <div class="osteobook-actions">
            <button type="button" id="osteobook-btn-prev"
                    class="osteobook-btn osteobook-btn--secondary btn btn--secondary" style="display:none;">
                &larr; <?php esc_html_e( 'Précédent', 'osteo-book' ); ?>
            </button>
            <button type="button" id="osteobook-btn-next"
                    class="osteobook-btn osteobook-btn--primary btn btn--primary" disabled>
                <?php esc_html_e( 'Suivant', 'osteo-book' ); ?>
            </button>
            <button type="submit" id="osteobook-btn-submit"
                    class="osteobook-btn osteobook-btn--primary btn btn--primary" style="display:none;">
                <?php esc_html_e( 'Confirmer la réservation', 'osteo-book' ); ?>
            </button>
        </div>

    </form>
</div>
