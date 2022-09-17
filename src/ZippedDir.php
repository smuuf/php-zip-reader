<?php

declare(strict_types=1);

namespace Smuuf\ZipReader;

use Smuuf\ZipReader\Exc\InvalidPathError;

class ZippedDir extends ZippedEntry implements \Countable {

	use \Smuuf\StrictObject;

	/**
	 * @param array<ZippedEntry> $entries
	 */
	public function __construct(
		ZipReader $zipReader,
		string $path,
		protected array $entries,
	) {

		if (!Helpers::isDirPath($path)) {
			throw new InvalidPathError(
				"Cannot create ZippedFile instance with path representing " .
				"a directory");
		}

		parent::__construct($zipReader, $path);

	}

	public function count(): int {
		return count($this->entries);
	}

	public function getBaseName(): string {
		return parent::getBaseName() . '/';
	}

	/**
	 * @return array<ZippedEntry>
	 */
	public function getEntries(): array {
		return $this->entries;
	}

	public function browse(string $path): ?ZippedEntry {

		$path = Helpers::normalizePath($path);

		// If the normalized path is empty, that actually means this directory.
		if ($path === '') {
			return $this;
		}

		$tuple = explode('/', $path, 2);

		$first = $tuple[0];
		$rest = $tuple[1] ?? null;

		// If the $rest is null, the path had no '/' in it and thus the $first
		// represents a file.
		if ($rest === null) {
			return $this->entries[$first] ?? null;
		}

		// The $rest is not null, so the $first represents another directory
		// inside current directory - append '/' so we can find it in
		// our $entries property (dirs have keys ending with '/').
		$first = "$first/";

		// Such directory does not exist in current directory - return null;
		if (!isset($this->entries[$first])) {
			return null;
		}

		/** @var ZippedDir */
		$zippedDir = $this->entries[$first];
		return $zippedDir->browse($rest);

	}

}
