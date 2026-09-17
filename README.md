# Toggle Group Field for Silverstripe CMS

Form field subclasses that render as a segmented "toggle group" control -
styled after [shadcn/ui's toggle group component](https://ui.shadcn.com/docs/components/aria/toggle-group)
- instead of a list of radio buttons or checkboxes.

* `ToggleGroupField` is a drop-in replacement for `OptionsetField` (single selection)
* `ToggleGroupSetField` is a drop-in replacement for `CheckboxSetField` (multiple selection)

Both fields work anywhere a standard `getCMSFields()` field can be used,
including inside [Elemental](https://github.com/silverstripe/silverstripe-elemental)
content blocks.

![ToggleGroupField and ToggleGroupSetField on a standard Page](docs/en/images/toggle-group-field-page.png)

*Above: `ToggleGroupField` (single-selection "Alignment") and
`ToggleGroupSetField` (multi-selection "Available days") added to a Page's
`getCMSFields()`.*

![ToggleGroupField and ToggleGroupSetField inside an Elemental block](docs/en/images/toggle-group-field-elemental.png)

*Above: the same two fields added to a `DNADesign\Elemental\Models\BaseElement`
subclass's `getCMSFields()`, rendering identically inside the Elemental block
editor.*

## Requirements

* PHP ^8.3
* silverstripe/framework ^6
* dnadesign/silverstripe-elemental ^6 (optional, only if you want to use the fields inside Elemental blocks)

For Silverstripe CMS 5, use the `1` branch (`^1`).

## Installation

```
composer require wilr/silverstripe-togglegroup-field
```

Then run a `dev/build` and flush (`?flush=1`).

## Usage

Both fields extend their core Silverstripe equivalent, so they accept the
same constructor arguments, support the same saving behaviour (including
`has_one`/`many_many` relations and `DBMultiEnum`/JSON-encoded values), the
same validation, and the same readonly/disabled transformations. Only the
rendering changes.

### Single selection - `ToggleGroupField`

```php
use Wilr\ToggleGroupField\Forms\ToggleGroupField;

public function getCMSFields()
{
    $fields = parent::getCMSFields();

    $fields->addFieldToTab('Root.Main', ToggleGroupField::create(
        'Alignment',
        'Alignment',
        [
            'left' => 'Left',
            'center' => 'Center',
            'right' => 'Right',
        ]
    ));

    return $fields;
}
```

### Multiple selection - `ToggleGroupSetField`

```php
use Wilr\ToggleGroupField\Forms\ToggleGroupSetField;

public function getCMSFields()
{
    $fields = parent::getCMSFields();

    $fields->addFieldToTab('Root.Main', ToggleGroupSetField::create(
        'Days',
        'Available days',
        [
            'mon' => 'Mon',
            'tue' => 'Tue',
            'wed' => 'Wed',
            'thu' => 'Thu',
            'fri' => 'Fri',
        ]
    ));

    return $fields;
}
```

### Inside an Elemental block

Add the fields to a `DNADesign\Elemental\Models\BaseElement` subclass exactly
as you would on a `Page`:

```php
use DNADesign\Elemental\Models\BaseElement;
use Wilr\ToggleGroupField\Forms\ToggleGroupField;

class MyElement extends BaseElement
{
    private static $db = [
        'Alignment' => 'Varchar',
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->addFieldToTab('Root.Main', ToggleGroupField::create(
            'Alignment',
            'Alignment',
            [
                'left' => 'Left',
                'center' => 'Center',
                'right' => 'Right',
            ]
        ));

        return $fields;
    }
}
```

Elemental's block editor is a schema-driven, React-rendered form (unlike a
classic `Page.getCMSFields()`, which is rendered server-side as plain HTML).
For that context the module registers `ToggleGroupField` and
`ToggleGroupSetField` React components (`client/dist/js/toggle-group-react.js`,
loaded in the CMS via `LeftAndMain.extra_requirements_javascript`). They
render the same markup as the templates, so styling and icons are identical,
and are bound to the form state so values save inline and in the "add block"
modal.

### Style variants

```php
ToggleGroupField::create(/* ... */)
    ->setButtonsBlock(true)  // stretch options to fill the available width
    ->setButtonsSmall(true); // render a more compact button size
```

### Icons on option labels

Both fields can show a Silverstripe CMS font icon and/or custom SVG beside
(or instead of) each option's text label.

#### CMS font icons

Use the same icon identifiers as `FormAction::setIcon()` (with or without
the `font-icon-` prefix). Browse the available glyphs in
`vendor/silverstripe/admin/client/src/font/icons-reference.html` after
installing `silverstripe/admin`.

```php
ToggleGroupField::create(
    'Alignment',
    'Alignment',
    [
        'left' => 'Left',
        'center' => 'Center',
        'right' => 'Right',
    ]
)->setOptionIcons([
    'left' => 'angle-left',
    'center' => 'dot-3',
    'right' => 'angle-right',
]);
```

#### Custom SVG

Pass a full `<svg>...</svg>` element, or one or more shape elements such as
`<path>` (automatically wrapped in a 24×24 `viewBox` SVG that inherits the
button colour via `currentColor`). SVG is rendered as trusted HTML - only
pass markup you control.

```php
ToggleGroupField::create(
    'Layout',
    'Layout',
    [
        'list' => 'List',
        'grid' => 'Grid',
    ]
)->setOptionIcons([
    'list' => '<path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-width="2" fill="none"/>',
    'grid' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><rect x="3" y="3" width="8" height="8"/><rect x="13" y="3" width="8" height="8"/><rect x="3" y="13" width="8" height="8"/><rect x="13" y="13" width="8" height="8"/></svg>',
]);
```

#### Icon-only buttons

Hide the text visually while keeping it available to screen readers through
the associated `<label>`:

```php
ToggleGroupField::create(/* ... */)
    ->setOptionIcons([
        'left' => 'angle-left',
        'center' => 'dot-3',
        'right' => 'angle-right',
    ])
    ->setIconsOnly(true);
```

## Accessibility

Each option is rendered as a real `<input type="radio">` or
`<input type="checkbox">` paired with a `<label>` - the toggle-look buttons
are just CSS applied to native, always-keyboard-accessible form controls
(visually hidden with a `clip`-based technique rather than `display: none`,
so they remain focusable and readable by screen readers). This means:

* The field works correctly with no JavaScript at all.
* Tab moves focus in and out of the group as normal.
* For `ToggleGroupField`, arrow keys move the selection between options -
  this is native browser behaviour for radio inputs sharing the same `name`.
* For `ToggleGroupSetField`, arrow keys move focus (without changing
  selection) between checkboxes, matching the
  [WAI-ARIA toggle group keyboard pattern](https://ui.shadcn.com/docs/components/aria/toggle-group).
  This is implemented in `client/src/js/toggle-group.js` since checkboxes
  have no native roving-focus behaviour.
* The wrapping element uses `role="radiogroup"` (`ToggleGroupField`) or
  `role="group"` (`ToggleGroupSetField`) to describe the relationship
  between the options to assistive technology.

## Running the tests

```
composer install
vendor/bin/phpunit
```

## License

See [LICENSE](LICENSE)
