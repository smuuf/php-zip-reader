<?php

declare(strict_types=1);

namespace Smuuf\ZipReader;

abstract class ZippedEntry {

	use \Smuuf\StrictObject;

	/** @var StatArray */
	protected ?array $stat = null;

	public function __construct(
		protected ZipReader $zipReader,
		protected string $path,
	) {}

	public function getPath(): string {
		return $this->path;
	}

	public function getBaseName(): string {
		return basename($this->path);
	}

	/** @return StatArray */
	public function getStat(): array {

		if ($this->stat !== null) {
			return $this->stat;
		}

		return $this->stat = $this->zipReader->getStat($this->path);

	}

}
