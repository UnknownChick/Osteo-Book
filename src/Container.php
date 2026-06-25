<?php

declare(strict_types=1);

namespace OsteoBook;

class Container {

	/** @var array<string, callable> */
	private array $bindings = [];

	/** @var array<string, mixed> */
	private array $instances = [];

	public function bind( string $abstract, callable $factory ): void {
		$this->bindings[ $abstract ] = $factory;
	}

	public function make( string $abstract ): mixed {
		if ( isset( $this->instances[ $abstract ] ) ) {
			return $this->instances[ $abstract ];
		}

		if ( ! isset( $this->bindings[ $abstract ] ) ) {
			throw new \RuntimeException( "No binding found for: {$abstract}" );
		}

		$this->instances[ $abstract ] = ( $this->bindings[ $abstract ] )();

		return $this->instances[ $abstract ];
	}
}
