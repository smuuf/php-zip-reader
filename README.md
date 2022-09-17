# Zip reader for PHP 🤐

![PHP tests](https://github.com/smuuf/php-zip-reader/workflows/PHP%20tests/badge.svg)

You just need to read Zip files in PHP? Say no more! 😎 This is a ***simple-to-use library for browsing and reading Zip files with friendly API.*** Just reading, nothing else _(like creating or modifying Zip files - if you need that, look elsewhere)_.

## Installation
```bash
composer require smuuf/zip-reader
```

ℹ️ This package uses the [`\ZipArchive`](https://www.php.net/manual/en/fiber.suspend.php) class internally, so you need to have PHP [extension `Zip`](https://www.php.net/manual/en/zip.installation.php) installed and enabled.

## Usage

```php
<?php

use \Smuuf\StrictObject;
use \Smuuf\ZipReader\ZippedDir;
use \Smuuf\ZipReader\ZippedFile;

require __DIR__ . '/../bootstrap.php';

// Loads some existing Zip file. (Or throws \Smuuf\ZipReader\Exc\ZipOpenError)
$zipReader = new ZipReader('/some/zipped/file.zip');

// This returns a dict array of items that are present in the root directory
// of the zip. For example:.
// [
// 	 'dir_a/' => ZippedDir object,
// 	 'dir_b/' => ZippedDir object,
// 	 'some_file_a.txt' => ZippedFile object,
// ]
$entries = $zipReader->getEntries();

// When browsing, directory path must have a trailing slash.
$item = $zipReader->browse('dir_a/'); // Instance of ZippedDir.
$item = $zipReader->browse('not_present_dir/'); // null
$item = $zipReader->browse('some_file_a.txt'); // Instance of ZippedFile.
$item = $zipReader->browse('not_present_dir/');  // null

// You can do nested browsing.
$dir = $zipReader->browse('dir_a/inner_dir/'); // Instance of ZippedDir.
$somefile = $zipReader->browse('dir_a/inner_dir/some_file.txt'); // Instance of ZippedFile.

// You can also browse further in the ZippedDir object.
// This will return ZippedFile that represents 'dir_a/inner_dir/some_other_file.txt'.
$someOtherFile = $dir->browse('some_other_file.txt');

// You can get contents of zipped file (done lazily).
$bytes = $someOtherFile->read();

// You can get stat array of a zipped file or a dir (done lazily).
// See https://www.php.net/manual/en/ziparchive.statname.php to see what
// items the stat array can  returns.
$fileStat = $someOtherFile->stat();
$dirStat = $dir->stat();
```
