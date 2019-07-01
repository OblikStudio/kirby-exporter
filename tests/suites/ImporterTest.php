<?php

use PHPUnit\Framework\TestCase;
use KirbyOutsource\Importer;
use Kirby\Data\Txt;

final class ImporterTest extends TestCase {
  private static $data;
  private static $items;

  public static function setUpBeforeClass (): void {
    kirby()->impersonate('kirby');

    $import = json_decode(file_get_contents(__DIR__ . '/imports/basic.json'), true);
    $importer = new Importer([
      'language' => 'bg'
    ]);
    $importer->import($import);

    $importResult = file_get_contents(realpath(__DIR__ . './../kirby/content/2_import-basic/import.bg.txt'));
    self::$data = Txt::decode($importResult);
    self::$items = Yaml::decode(self::$data['items']);
  }

  public function testDoesNotContainTitle () {
    // The `Title` field is present in the default txt by default but it
    // shouldn't appear in the translation because it's not in the blueprint.
    $this->assertArrayNotHasKey('title', self::$data);
  }

  public function testDefaultValuePreserved () {
    // If a field is not present in the import but exists in the blueprint and
    // in the default txt, it should be preserved.
    $this->assertEquals('default', self::$data['f1']);
  }

  public function testValueImported () {
    // If a field exists in both the default txt and the import, the imported
    // value should override the default one.
    $this->assertEquals('both imported', self::$data['f2']);
  }

  public function testImportedValueAdded () {
    // If a value exists only in the import but the field is listed in the
    // blueprint, that value should be added.
    $this->assertEquals('import', self::$data['f3']);
  }

  public function testMissingValueIsMissing () {
    // The `f4` field is defined in the blueprint, but absent in both the
    // default txt and the import. It should not appear in the translation.
    $this->assertArrayNotHasKey('f4', self::$data);
  }

  public function testStructureSecondEntryPreserved () {
    // Even though the import contains one entry, the second entry from the
    // default txt should be copied.
    $this->assertEquals('default', self::$items[1]['f1']);
  }

  public function testStructureDefaultValuePreserved () {
    $this->assertEquals('default', self::$items[0]['f1']);
  }

  public function testStructureValueImported () {
    $this->assertEquals('both imported', self::$items[0]['f2']);
  }

  public function testStructureImportedValueAdded () {
    $this->assertEquals('import', self::$items[0]['f3']);
  }

  public function testStructureMissingValueIsMissing () {
    $this->assertArrayNotHasKey('f4', self::$items[0]);
  }
}
