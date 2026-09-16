<?php

namespace Wilr\ToggleGroupField\Tests\Forms;

use DNADesign\Elemental\Models\BaseElement;
use SilverStripe\Dev\TestOnly;
use SilverStripe\Forms\FieldList;
use Wilr\ToggleGroupField\Forms\ToggleGroupField;
use Wilr\ToggleGroupField\Forms\ToggleGroupSetField;

/**
 * A minimal Elemental content block used to prove ToggleGroupField and
 * ToggleGroupSetField can be added to a BaseElement's getCMSFields() and
 * behave the same way they do on a standard Page.
 */
class ToggleGroupTestElement extends BaseElement implements TestOnly
{
    private static $table_name = 'ToggleGroupTestElement';

    private static $db = [
        'Alignment' => 'Varchar',
        'Interests' => 'Varchar(255)',
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->addFieldsToTab('Root.Main', [
            ToggleGroupField::create(
                'Alignment',
                'Alignment',
                [
                    'left' => 'Left',
                    'center' => 'Center',
                    'right' => 'Right',
                ]
            ),
            ToggleGroupSetField::create(
                'Interests',
                'Interests',
                [
                    'mon' => 'Mon',
                    'tue' => 'Tue',
                    'wed' => 'Wed',
                ]
            ),
        ]);

        return $fields;
    }
}
