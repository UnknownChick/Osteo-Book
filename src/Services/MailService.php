<?php

declare(strict_types=1);

namespace OsteoBook\Services;

use OsteoBook\DTO\ReservationDTO;

class MailService {

	public function notifyCustomer( ReservationDTO $dto ): void {
		wp_mail(
			$dto->email,
			sprintf(
				__( '[OsteoBook] Confirmation – %s à %s', 'osteo-book' ),
				$dto->date,
				$dto->slot
			),
			$this->buildCustomerMessage( $dto ),
			[ 'Content-Type: text/html; charset=UTF-8' ]
		);
	}

	public function notifyAdmin( ReservationDTO $dto ): void {
		wp_mail(
			(string) get_option( 'admin_email' ),
			sprintf(
				__( '[OsteoBook] Nouvelle réservation – %s %s – %s à %s', 'osteo-book' ),
				$dto->firstName,
				$dto->lastName,
				$dto->date,
				$dto->slot
			),
			$this->buildAdminMessage( $dto ),
			[ 'Content-Type: text/html; charset=UTF-8' ]
		);
	}

	private function buildCustomerMessage( ReservationDTO $dto ): string {
		ob_start();
		$siteName = get_bloginfo( 'name' );
		?>
		<!DOCTYPE html>
		<html>
		<body style="font-family:sans-serif;color:#1f2937;max-width:600px;margin:0 auto">
			<h2 style="color:#4f46e5"><?php echo esc_html( $siteName ); ?> – Confirmation de rendez-vous</h2>
			<p>Bonjour <?php echo esc_html( $dto->firstName . ' ' . $dto->lastName ); ?>,</p>
			<p>Votre rendez-vous a bien été enregistré :</p>
			<table style="border-collapse:collapse;width:100%">
				<tr><td style="padding:8px;background:#f3f4f6;font-weight:bold">Date</td><td style="padding:8px"><?php echo esc_html( $dto->date ); ?></td></tr>
				<tr><td style="padding:8px;background:#f3f4f6;font-weight:bold">Créneau</td><td style="padding:8px"><?php echo esc_html( $dto->slot ); ?></td></tr>
				<tr><td style="padding:8px;background:#f3f4f6;font-weight:bold">Lieu</td><td style="padding:8px"><?php echo nl2br( esc_html( $dto->address ) ); ?></td></tr>
				<tr><td style="padding:8px;background:#f3f4f6;font-weight:bold">Animal</td><td style="padding:8px"><?php echo esc_html( $dto->animalName . ( $dto->animalType ? ' (' . $dto->animalType . ')' : '' ) ); ?></td></tr>
			</table>
			<?php if ( $dto->message ): ?>
			<p><strong>Votre message :</strong> <?php echo esc_html( $dto->message ); ?></p>
			<?php endif; ?>
			<p>À bientôt !</p>
		</body>
		</html>
		<?php
		return (string) ob_get_clean();
	}

	private function buildAdminMessage( ReservationDTO $dto ): string {
		ob_start();
		?>
		<!DOCTYPE html>
		<html>
		<body style="font-family:sans-serif;color:#1f2937;max-width:600px;margin:0 auto">
			<h2 style="color:#4f46e5">Nouvelle réservation OsteoBook</h2>
			<table style="border-collapse:collapse;width:100%">
				<tr><td style="padding:8px;background:#f3f4f6;font-weight:bold">Prénom</td><td style="padding:8px"><?php echo esc_html( $dto->firstName ); ?></td></tr>
				<tr><td style="padding:8px;background:#f3f4f6;font-weight:bold">Nom</td><td style="padding:8px"><?php echo esc_html( $dto->lastName ); ?></td></tr>
				<tr><td style="padding:8px;background:#f3f4f6;font-weight:bold">Email</td><td style="padding:8px"><?php echo esc_html( $dto->email ); ?></td></tr>
				<tr><td style="padding:8px;background:#f3f4f6;font-weight:bold">Téléphone</td><td style="padding:8px"><?php echo esc_html( $dto->phone ); ?></td></tr>
				<tr><td style="padding:8px;background:#f3f4f6;font-weight:bold">Adresse</td><td style="padding:8px"><?php echo nl2br( esc_html( $dto->address ) ); ?></td></tr>
				<tr><td style="padding:8px;background:#f3f4f6;font-weight:bold">Animal</td><td style="padding:8px"><?php echo esc_html( $dto->animalName . ( $dto->animalType ? ' (' . $dto->animalType . ')' : '' ) ); ?></td></tr>
				<tr><td style="padding:8px;background:#f3f4f6;font-weight:bold">Date</td><td style="padding:8px"><?php echo esc_html( $dto->date ); ?></td></tr>
				<tr><td style="padding:8px;background:#f3f4f6;font-weight:bold">Créneau</td><td style="padding:8px"><?php echo esc_html( $dto->slot ); ?></td></tr>
				<?php if ( $dto->message ): ?>
				<tr><td style="padding:8px;background:#f3f4f6;font-weight:bold">Message</td><td style="padding:8px"><?php echo esc_html( $dto->message ); ?></td></tr>
				<?php endif; ?>
			</table>
		</body>
		</html>
		<?php
		return (string) ob_get_clean();
	}
}
