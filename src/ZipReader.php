<?php

declare(strict_types=1);

namespace Smuuf\ZipReader;

use Smuuf\ZipReader\Exc\ZipOpenError;

class ZipReader {

	use \Smuuf\StrictObject;

	private \ZipArchive $zip;
	private ZippedDir $tree;

	public function __construct(string $path) {

		if (!realpath($path)) {
			throw new ZipOpenError("Zip file '$path' not found");
		}

		if (!is_readable($path)) {
			throw new ZipOpenError("Zip file '$path' is not readable");
		}

		$this->tree = $this->loadZip($path);

	}

	private function loadZip(string $path): ZippedDir {

		$this->zip = new \ZipArchive;
		$this->zip->open($path);

		$rootDir = new ZippedDirBuilder($this, '/');

		// Iterate over a list of all entries in a zip and build a tree
		// structure based on the entries' paths.
		$fileCount = $this->zip->numFiles;
		for ($i = 0; $i < $fileCount; $i++) {

			$path = $this->zip->getNameIndex($i);
			if ($path === false) {
				continue;
			}

			$isDir = Helpers::isDirPath($path);

			$currentDir = $rootDir;
			$pathParts = explode('/', $path);

			// We want to iterate over all parts of the path except the last,
			// which we'll handle after the foreach.
			array_pop($pathParts);

			foreach ($pathParts as $part) {

				if (!$part) {
					continue;
				}

				$currentDir = $currentDir->getDir($part);

			}

			// If the path is a dir, the tree leading up to it is now created.
			// If it's a file, add that file to it.
			if (!$isDir) {
				$currentDir->addFile(new ZippedFile($this, $path));
			}

		}

		return $rootDir->build();

	}

	public function browse(string $path): ?ZippedEntry {
		return $this->tree->browse($path);
	}

	public function readFile(string $path): ?string {

		$normalized = Helpers::normalizePath($path);
		$read = $this->zip->getFromName($normalized);
		return $read !== false
			? $read
			: null;

	}

	/**
	 * @return array<string, mixed>
	 */
	public function getStat(string $path): array {

		$normalized = Helpers::normalizePath($path);
		return $normalized !== ''
			? ($this->zip->statName($normalized) ?: [])
			: [];

	}

}
