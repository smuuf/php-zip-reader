<?php

declare(strict_types=1);

namespace Smuuf\ZipReader;

use Smuuf\ZipReader\Exc\InvalidPathError;

class ZippedFile extends ZippedEntry {

	use \Smuuf\StrictObject;

	private ?string $bytes = null;

	public function __construct(
		ZipReader $zipReader,
		string $path,
	) {

		if (Helpers::isDirPath($path)) {
			throw new InvalidPathError(
				"Cannot create ZippedFile instance with path representing " .
				"a directory");
		}

		parent::__construct($zipReader, $path);

	}

	public function read(): string {

		if ($this->bytes !== null) {
			return $this->bytes;
		}

		return $this->bytes = $this->zipReader->readFile($this->path) ?? '';

	}

}
