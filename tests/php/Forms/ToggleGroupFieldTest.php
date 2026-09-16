<?php

namespace Wilr\ToggleGroupField\Tests\Forms;

use SilverStripe\Dev\SapphireTest;
use Wilr\ToggleGroupField\Forms\ToggleGroupField;

class ToggleGroupFieldTest extends SapphireTest
{
    protected static $extra_dataobjects = [
        ToggleGroupTestObject::class,
    ];

    private function getField(): ToggleGroupField
    {
        return ToggleGroupField::create(
            'Alignment',
            'Alignment',
            [
                'left' => 'Left',
                'center' => 'Center',
                'right' => 'Right',
            ]
        );
    }

    public function testExtraClassIsApplied()
    {
        $field = $this->getField();

        $this->assertStringContainsString('toggle-group-field', $field->extraClass());
    }

    public function testAttributesUseRadiogroupRole()
    {
        $field = $this->getField();
        $attributes = $field->getAttributes();

        $this->assertSame('radiogroup', $attributes['role']);
    }

    public function testSchemaComponentFallsBackToServerRenderedMarkup()
    {
        // OptionsetField declares its own React component, which would
        // otherwise be used instead of our template inside schema-driven
        // forms (e.g. Elemental's GridField-based block editor).
        $field = $this->getField();

        $this->assertSame('FormField', $field->getSchemaComponent());
    }

    public function testFieldRendersARadioInputPerOption()
    {
        $field = $this->getField();
        $html = (string) $field->Field();

        $this->assertSame(3, substr_count($html, 'type="radio"'));
        $this->assertStringContainsString('>Left<', $html);
        $this->assertStringContainsString('>Center<', $html);
        $this->assertStringContainsString('>Right<', $html);
    }

    public function testSelectedValueIsMarkedChecked()
    {
        $field = $this->getField();
        $field->setValue('center');

        $html = (string) $field->Field();

        $this->assertSame(1, substr_count($html, 'checked'));
        $this->assertMatchesRegularExpression('/<input[^>]*value="center"[^>]*checked[^>]*\/>/s', $html);
    }

    public function testSetButtonsBlockTogglesExtraClass()
    {
        $field = $this->getField();
        $field->setButtonsBlock(true);
        $this->assertStringContainsString('toggle-group-field--block', $field->extraClass());

        $field->setButtonsBlock(false);
        $this->assertStringNotContainsString('toggle-group-field--block', $field->extraClass());
    }

    public function testSetButtonsSmallTogglesExtraClass()
    {
        $field = $this->getField();
        $field->setButtonsSmall(true);
        $this->assertStringContainsString('toggle-group-field--small', $field->extraClass());

        $field->setButtonsSmall(false);
        $this->assertStringNotContainsString('toggle-group-field--small', $field->extraClass());
    }

    public function testSaveIntoWritesTheSelectedValue()
    {
        $field = $this->getField();
        $field->setValue('right');

        $record = ToggleGroupTestObject::create();
        $field->saveInto($record);

        $this->assertSame('right', $record->Alignment);
    }

    public function testFieldIsReadonlyTransformable()
    {
        $field = $this->getField();
        $field->setValue('left');

        $readonlyField = $field->performReadonlyTransformation();

        $this->assertTrue($readonlyField->isReadonly());
    }
}
