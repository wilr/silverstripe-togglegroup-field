<?php

namespace Wilr\ToggleGroupField\Tests\Forms;

use SilverStripe\Dev\SapphireTest;

/**
 * Confirms ToggleGroupField and ToggleGroupSetField work when added to an
 * Elemental BaseElement's getCMSFields(), the same way they do on a normal
 * Page. Skipped automatically if dnadesign/silverstripe-elemental isn't
 * installed (it's a require-dev/suggest, not a hard dependency).
 */
class ToggleGroupElementalTest extends SapphireTest
{
    protected static $extra_dataobjects = [
        ToggleGroupTestElement::class,
    ];

    protected function setUp(): void
    {
        if (!class_exists(ToggleGroupTestElement::class)) {
            $this->markTestSkipped('silverstripe-elemental is not installed');
        }

        parent::setUp();
    }

    public function testFieldsAreAddedToTheElementCMSFields()
    {
        $element = ToggleGroupTestElement::create();
        $fields = $element->getCMSFields();

        $this->assertNotNull($fields->dataFieldByName('Alignment'));
        $this->assertNotNull($fields->dataFieldByName('Interests'));
    }

    public function testFieldsRenderAndSaveWithinTheElement()
    {
        $element = ToggleGroupTestElement::create();
        $fields = $element->getCMSFields();

        $alignment = $fields->dataFieldByName('Alignment');
        $alignment->setValue('right');
        $alignment->saveInto($element);

        $interests = $fields->dataFieldByName('Interests');
        $interests->setValue(['mon', 'tue']);
        $interests->saveInto($element);

        $element->write();

        $this->assertSame('right', $element->Alignment);
        $this->assertSame(['mon', 'tue'], json_decode($element->Interests, true));
        $this->assertStringContainsString('toggle-group-field', (string) $alignment->Field());
        $this->assertStringContainsString('toggle-group-field', (string) $interests->Field());
    }
}
