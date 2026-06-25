<?php

declare(strict_types=1);

namespace OsteoBook\Front;

use OsteoBook\Container;

class Shortcode {

	public function __construct(
		private readonly Container $container
	) {}

	/** @param array<string,mixed> $atts */
	public function render( array $atts = [] ): string {
		shortcode_atts( [], $atts, 'osteo_book' );

		ob_start();
		include OSTEOBOOK_PATH . 'resources/views/form.php';

		return (string) ob_get_clean();
	}
}
