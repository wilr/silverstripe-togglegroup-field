<?php

namespace Wilr\ToggleGroupField\Forms;

use SilverStripe\Model\ArrayData;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\View\Requirements;

/**
 * Shared behaviour between ToggleGroupField and ToggleGroupSetField.
 */
trait ToggleGroupFieldTrait
{
    /**
     * Map of option value => icon. Icons are either a Silverstripe CMS font
     * icon identifier (e.g. "left-dir", matching FormAction::setIcon()) or
     * custom SVG markup / path data.
     *
     * @var array<string|int, string>
     */
    protected array $optionIcons = [];

    /**
     * When true, option titles are visually hidden so each button shows only
     * its icon. Titles remain in the DOM for screen readers via the label.
     */
    protected bool $iconsOnly = false;

    /**
     * Includes the CSS/JS required to render and operate the toggle group.
     * Safe to call multiple times per request as Requirements de-dupes.
     */
    protected function requireToggleGroupAssets(): void
    {
        Requirements::css('wilr/silverstripe-togglegroup-field: client/dist/styles/toggle-group.css');
        Requirements::javascript('wilr/silverstripe-togglegroup-field: client/dist/js/toggle-group.js');
    }

    /**
     * Stretch each option to share the available width evenly, rather than
     * the default of each option being sized to fit its content.
     *
     * @return $this
     */
    public function setButtonsBlock(bool $block = true): static
    {
        if ($block) {
            $this->addExtraClass('toggle-group-field--block');
        } else {
            $this->removeExtraClass('toggle-group-field--block');
        }

        return $this;
    }

    /**
     * Render the toggle group using a more compact button size.
     *
     * @return $this
     */
    public function setButtonsSmall(bool $small = true): static
    {
        if ($small) {
            $this->addExtraClass('toggle-group-field--small');
        } else {
            $this->removeExtraClass('toggle-group-field--small');
        }

        return $this;
    }

    /**
     * Assign icons to individual options, keyed by option value.
     *
     * Font icons use the same identifiers as {@see \SilverStripe\Forms\FormAction::setIcon()}
     * (with or without a `font-icon-` prefix), e.g. `left-dir`, `picture`,
     * `lock`. Custom SVG can be passed as a full `<svg>...</svg>` element or
     * as one or more `<path>` / shape elements (wrapped in a 24×24 viewBox).
     *
     * SVG content is rendered as trusted HTML - only pass markup you control.
     *
     * @param array<string|int, string> $icons
     * @return $this
     */
    public function setOptionIcons(array $icons): static
    {
        $this->optionIcons = $icons;

        return $this;
    }

    /**
     * @return array<string|int, string>
     */
    public function getOptionIcons(): array
    {
        return $this->optionIcons;
    }

    /**
     * Hide option titles visually so buttons show icons only. Titles stay
     * associated with each input for assistive technology.
     *
     * @return $this
     */
    public function setIconsOnly(bool $iconsOnly = true): static
    {
        $this->iconsOnly = $iconsOnly;

        if ($iconsOnly) {
            $this->addExtraClass('toggle-group-field--icons-only');
        } else {
            $this->removeExtraClass('toggle-group-field--icons-only');
        }

        return $this;
    }

    public function getIconsOnly(): bool
    {
        return $this->iconsOnly;
    }

    /**
     * Merge icon template fields onto an option ArrayData produced by the
     * parent OptionsetField / CheckboxSetField.
     */
    protected function augmentOptionWithIcon(ArrayData $option): ArrayData
    {
        $value = $option->getField('Value');
        $icon = $this->getOptionIconForValue($value);

        $option->setField('HasIcon', $icon !== null);
        $option->setField('Icon', null);
        $option->setField('IconSvg', null);
        $option->setField('IconsOnly', $this->getIconsOnly());

        if ($icon === null) {
            return $option;
        }

        if ($this->isSvgIcon($icon)) {
            $option->setField(
                'IconSvg',
                DBHTMLText::create()->setValue($this->normaliseSvgIcon($icon))
            );
        } else {
            $option->setField('Icon', $this->normaliseFontIconName($icon));
        }

        return $option;
    }

    /**
     * @param mixed $value
     */
    protected function getOptionIconForValue($value): ?string
    {
        $icons = $this->getOptionIcons();

        if (array_key_exists($value, $icons)) {
            return $icons[$value] !== '' ? (string) $icons[$value] : null;
        }

        $stringValue = (string) $value;
        if (array_key_exists($stringValue, $icons)) {
            return $icons[$stringValue] !== '' ? (string) $icons[$stringValue] : null;
        }

        return null;
    }

    protected function isSvgIcon(string $icon): bool
    {
        $trimmed = ltrim($icon);

        return str_starts_with($trimmed, '<');
    }

    /**
     * Normalise a font icon identifier to the bare name used with
     * `font-icon-$Icon` in templates (same convention as FormAction).
     */
    protected function normaliseFontIconName(string $icon): string
    {
        $icon = trim($icon);
        $icon = preg_replace('/^font-icon-/', '', $icon) ?? $icon;
        $icon = preg_replace('/[^a-zA-Z0-9_-]/', '', $icon) ?? '';

        return $icon;
    }

    /**
     * Accept a full SVG element or raw inner shapes and ensure a usable
     * inline SVG is returned for the template.
     */
    protected function normaliseSvgIcon(string $icon): string
    {
        $icon = trim($icon);

        if (preg_match('/^<svg\b/i', $icon)) {
            return $icon;
        }

        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"'
            . ' width="1em" height="1em" fill="currentColor" aria-hidden="true">'
            . $icon
            . '</svg>';
    }
}
