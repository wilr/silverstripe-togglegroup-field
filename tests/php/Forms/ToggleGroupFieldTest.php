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

    public function testOptionFontIconsAreRendered()
    {
        $field = $this->getField();
        $field->setOptionIcons([
            'left' => 'angle-left',
            'center' => 'font-icon-dot-3',
            'right' => 'angle-right',
        ]);

        $html = (string) $field->Field();

        $this->assertStringContainsString('font-icon-angle-left', $html);
        $this->assertStringContainsString('font-icon-dot-3', $html);
        $this->assertStringContainsString('font-icon-angle-right', $html);
        $this->assertStringContainsString('toggle-group__icon', $html);
        $this->assertStringContainsString('>Left<', $html);
    }

    public function testOptionSvgIconsAcceptPathsAndFullMarkup()
    {
        $field = $this->getField();
        $field->setOptionIcons([
            'left' => '<path d="M4 12h16"/>',
            'center' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">'
                . '<circle cx="12" cy="12" r="8"/></svg>',
        ]);

        $html = (string) $field->Field();

        $this->assertStringContainsString('viewBox="0 0 24 24"', $html);
        $this->assertStringContainsString('<path d="M4 12h16"/>', $html);
        $this->assertStringContainsString('<circle cx="12" cy="12" r="8"/>', $html);
        $this->assertStringNotContainsString('font-icon-', $html);
    }

    public function testIconsOnlyHidesTitlesVisually()
    {
        $field = $this->getField();
        $field->setOptionIcons(['left' => 'angle-left']);
        $field->setIconsOnly(true);

        $html = (string) $field->Field();

        $this->assertStringContainsString('toggle-group-field--icons-only', $field->extraClass());
        $this->assertStringContainsString('toggle-group__label--icon-only', $html);
        $this->assertStringContainsString('toggle-group__title--sr-only', $html);
        $this->assertStringContainsString('>Left<', $html);
    }
}
