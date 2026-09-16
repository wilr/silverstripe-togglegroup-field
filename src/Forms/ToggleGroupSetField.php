<?php

namespace Wilr\ToggleGroupField\Forms;

use SilverStripe\Forms\CheckboxSetField;

/**
 * A multi-selection toggle group, styled after the shadcn/ui ARIA toggle
 * group component. Functionally and semantically this is a drop-in
 * replacement for {@link CheckboxSetField} (which it extends) - it renders
 * a native checkbox for each option and only changes how those options are
 * displayed, so saving (including many_many relation lists), validation and
 * readonly/disabled transformations all work exactly as they do for
 * CheckboxSetField.
 *
 * <code>
 * ToggleGroupSetField::create(
 *     'Days',
 *     'Available days',
 *     [
 *         'mon' => 'Mon',
 *         'tue' => 'Tue',
 *         'wed' => 'Wed',
 *     ]
 * );
 * </code>
 *
 * @see ToggleGroupField for a single-selection equivalent based on
 * OptionsetField.
 */
class ToggleGroupSetField extends CheckboxSetField
{
    use ToggleGroupFieldTrait;

    public function __construct($name, $title = null, $source = [], $value = null)
    {
        parent::__construct($name, $title, $source, $value);

        $this->addExtraClass('toggle-group-field');
        $this->requireToggleGroupAssets();

        // CheckboxSetField declares a dedicated React component for
        // rendering inside schema-driven forms (e.g. Elemental's
        // GridField-based block editor), which would otherwise bypass our
        // template entirely. Falling back to the generic component makes
        // those contexts use our server-rendered markup, same as a classic
        // Page.getCMSFields().
        $this->setSchemaComponent('FormField');
    }

    public function getAttributes()
    {
        return array_merge(
            parent::getAttributes(),
            ['role' => 'group']
        );
    }
}
