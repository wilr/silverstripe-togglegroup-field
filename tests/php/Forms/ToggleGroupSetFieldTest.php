<?php

namespace Wilr\ToggleGroupField\Tests\Forms;

use SilverStripe\Dev\SapphireTest;
use Wilr\ToggleGroupField\Forms\ToggleGroupSetField;

class ToggleGroupSetFieldTest extends SapphireTest
{
    protected static $extra_dataobjects = [
        ToggleGroupTestObject::class,
    ];

    private function getField(): ToggleGroupSetField
    {
        return ToggleGroupSetField::create(
            'Interests',
            'Interests',
            [
                'mon' => 'Mon',
                'tue' => 'Tue',
                'wed' => 'Wed',
            ]
        );
    }

    public function testExtraClassIsApplied()
    {
        $field = $this->getField();

        $this->assertStringContainsString('toggle-group-field', $field->extraClass());
    }

    public function testAttributesUseGroupRole()
    {
        $field = $this->getField();
        $attributes = $field->getAttributes();

        $this->assertSame('group', $attributes['role']);
    }

    public function testSchemaUsesToggleGroupSetComponent()
    {
        $field = $this->getField();

        $this->assertSame('ToggleGroupSetField', $field->getSchemaComponent());
    }

    public function testFieldRendersACheckboxInputPerOption()
    {
        $field = $this->getField();
        $html = (string) $field->Field();

        $this->assertSame(3, substr_count($html, 'type="checkbox"'));
        $this->assertStringContainsString('>Mon<', $html);
        $this->assertStringContainsString('>Tue<', $html);
        $this->assertStringContainsString('>Wed<', $html);
    }

    public function testMultipleSelectedValuesAreMarkedChecked()
    {
        $field = $this->getField();
        $field->setValue(['mon', 'wed']);

        $html = (string) $field->Field();

        $this->assertSame(2, substr_count($html, 'checked'));
        $this->assertMatchesRegularExpression('/<input[^>]*value="mon"[^>]*checked[^>]*\/>/s', $html);
        $this->assertMatchesRegularExpression('/<input[^>]*value="wed"[^>]*checked[^>]*\/>/s', $html);
        $this->assertDoesNotMatchRegularExpression('/<input[^>]*value="tue"[^>]*checked[^>]*\/>/s', $html);
    }

    public function testSaveIntoWritesAJsonEncodedList()
    {
        $field = $this->getField();
        $field->setValue(['mon', 'tue']);

        $record = ToggleGroupTestObject::create();
        $field->saveInto($record);

        $this->assertSame(['mon', 'tue'], json_decode($record->Interests, true));
    }

    public function testSetButtonsBlockTogglesExtraClass()
    {
        $field = $this->getField();
        $field->setButtonsBlock(true);
        $this->assertStringContainsString('toggle-group-field--block', $field->extraClass());

        $field->setButtonsBlock(false);
        $this->assertStringNotContainsString('toggle-group-field--block', $field->extraClass());
    }

    public function testOptionIconsAreRenderedOnSetField()
    {
        $field = $this->getField();
        $field->setOptionIcons([
            'mon' => 'calendar',
            'tue' => '<path d="M5 12h14"/>',
        ]);

        $html = (string) $field->Field();

        $this->assertStringContainsString('font-icon-calendar', $html);
        $this->assertStringContainsString('<path d="M5 12h14"/>', $html);
        $this->assertStringContainsString('toggle-group__icon', $html);
    }
}
