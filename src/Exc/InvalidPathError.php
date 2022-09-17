<?php

declare(strict_types=1);

namespace Smuuf\ZipReader\Exc;

class InvalidPathError extends \RuntimeException implements ZipReaderInternalErrorInterface {}
