<?php

declare(strict_types=1);

namespace Smuuf\ZipReader;

use Smuuf\ZipReader\Exc\InvalidPathError;

/**
 * @internal
 */
final class ZippedDirBuilder {

	use \Smuuf\StrictObject;

	/** @var array<ZippedDirBuilder> */
	private array $dirs = [];

	/** @var array<ZippedFile> */
	private array $files = [];

	/**
	 * @param string $path Full path to the zipped dir represented by this
	 * builder.
	 */
	public function __construct(
		private ZipReader $zipReader,
		private string $path,
	) {
		if (!Helpers::isDirPath($path)) {
			throw new InvalidPathError(
				"Cannot create ZippedFile instance with path representing " .
				"a directory");
		}
	}

	public function getDir(string $name): ZippedDirBuilder {
		return $this->dirs["{$name}/"]
			??= new ZippedDirBuilder($this->zipReader, "{$this->path}{$name}/");
	}

	public function addFile(ZippedFile $entry): void {
		$this->files[$entry->getBaseName()] = $entry;
	}

	public function build(): ZippedDir {

		$entries = array_map(
			fn(ZippedDirBuilder $dirBuilder) => $dirBuilder->build(),
			$this->dirs,
		) + $this->files;

		return new ZippedDir($this->zipReader, $this->path, $entries);

	}

}
