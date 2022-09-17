<?php

declare(strict_types=1);

use Tester\Assert;
use Tester\Expect;

use Smuuf\ZipReader\ZipReader;
use Smuuf\ZipReader\ZippedDir;
use Smuuf\ZipReader\ZippedFile;

require __DIR__ . '/../bootstrap.php';

$z = new ZipReader(__DIR__ . '/data/example.zip');

//
// Directory path: "./"
//

/** @var ZippedDir */
$dir = $z->browse('/');

Assert::type(ZippedDir::class, $dir);
Assert::count(5, $dir);
Assert::equal([
	'dir_a/' => Expect::type(ZippedDir::class),
	'dir_b/' => Expect::type(ZippedDir::class),
	'file_root_empty.txt' => Expect::type(ZippedFile::class),
	'.file_root_hidden' => Expect::type(ZippedFile::class),
	'file_root.txt' => Expect::type(ZippedFile::class),
], $dir->getEntries());

//
// Nonexistent dir.
//

$dir = $z->browse('/NOT_FOUND/');
Assert::null($dir);

//
// Nonexistent file.
//

$file = $z->browse('/NOT_FOUND_FILE');
Assert::null($file);

//
// Directory path: "./dir_a/"
//

/** @var ZippedDir */
$dir = $z->browse('dir_a/');
Assert::type(ZippedDir::class, $dir);
Assert::count(4, $dir);
Assert::equal([
	'dir_ab/' => Expect::type(ZippedDir::class),
	'file_a_empty.txt' => Expect::type(ZippedFile::class),
	'.file_a_hidden' => Expect::type(ZippedFile::class),
	'file_a.txt' => Expect::type(ZippedFile::class),
], $dir->getEntries());

// Further browsing from the already fetched ZippedDir.
$innerDir = $dir->browse('dir_ab/');
Assert::type(ZippedDir::class, $innerDir);
Assert::count(3, $innerDir);

$innererFile = $dir->browse('dir_ab/file_ab_empty.txt');
Assert::type(ZippedFile::class, $innererFile);
Assert::type('int', $innererFile->getStat()['size']);

//
// Directory path: "./dir_a/dir_ab"
//

/** @var ZippedDir */
$dir = $z->browse('dir_a/dir_ab/');
Assert::type(ZippedDir::class, $dir);
Assert::count(3, $dir);
Assert::equal([
	'file_ab_empty.txt' => Expect::type(ZippedFile::class),
	'.file_ab_hidden' => Expect::type(ZippedFile::class),
	'file_ab.txt' => Expect::type(ZippedFile::class),
], $dir->getEntries());

//
// Directory path: "./dir_b"
//

/** @var ZippedDir */
$dir = $z->browse('dir_b/');
Assert::type(ZippedDir::class, $dir);
Assert::count(3, $dir);
Assert::equal([
	'file_b_empty.txt' => Expect::type(ZippedFile::class),
	'.file_b_hidden' => Expect::type(ZippedFile::class),
	'file_b.txt' => Expect::type(ZippedFile::class),
], $dir->getEntries());

//
// File path: "./dir_b/file_b_empty.txt"
//

$file = $z->readFile('dir_b/file_b_empty.txt');
Assert::same('', trim($file));

//
// File path: "./dir_b/file_b.txt"
//

$file = $z->readFile('dir_b/file_b.txt');
Assert::same('this_is_file_b', trim($file));
