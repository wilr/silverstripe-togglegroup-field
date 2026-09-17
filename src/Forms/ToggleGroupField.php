<?php

namespace Wilr\ToggleGroupField\Forms;

use SilverStripe\Forms\OptionsetField;

/**
 * A single-selection toggle group, styled after the shadcn/ui ARIA toggle
 * group component. Functionally and semantically this is a drop-in
 * replacement for {@link OptionsetField} (which it extends) - it renders a
 * native radio button for each option and only changes how those options
 * are displayed, so saving, validation, readonly/disabled transformations
 * and required-field behaviour all work exactly as they do for
 * OptionsetField.
 *
 * <code>
 * ToggleGroupField::create(
 *     'Alignment',
 *     'Alignment',
 *     [
 *         'left' => 'Left',
 *         'center' => 'Center',
 *         'right' => 'Right',
 *     ]
 * );
 * </code>
 *
 * @see ToggleGroupSetField for a multi-selection equivalent based on
 * CheckboxSetField.
 */
class ToggleGroupField extends OptionsetField
{
    use ToggleGroupFieldTrait;

    public function __construct($name, $title = null, $source = [], $value = null)
    {
        parent::__construct($name, $title, $source, $value);

        $this->addExtraClass('toggle-group-field');
        $this->requireToggleGroupAssets();

        // Schema-driven forms (e.g. Elemental's inline block editor) render
        // this component, registered in client/dist/js/toggle-group-react.js.
        $this->setSchemaComponent('ToggleGroupField');
    }

    protected function getFieldOption($value, $title, $odd)
    {
        return $this->augmentOptionWithIcon(parent::getFieldOption($value, $title, $odd));
    }

    public function getAttributes()
    {
        return array_merge(
            parent::getAttributes(),
            ['role' => 'radiogroup']
        );
    }
}
