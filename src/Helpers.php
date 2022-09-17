<?php

declare(strict_types=1);

namespace Smuuf\ZipReader;

abstract class Helpers {

	final private function __construct() {
		// Nope.
	}

	/**
	 * Returns `true` if the path string (which must originate from PHP's
	 * class `\ZipArchive`) represents a directory path (those always end with a
	 * slash - says the internet).
	 */
	public static function isDirPath(string $path): bool {
		return str_ends_with($path, '/');
	}

	public static function normalizePath(string $path): string {
		return ltrim(preg_replace('#/{2,}#', '/', $path) ?? '', '/');
	}

}
