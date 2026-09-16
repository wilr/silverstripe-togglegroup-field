<?php

namespace Wilr\ToggleGroupField\Forms;

use SilverStripe\View\Requirements;

/**
 * Shared behaviour between ToggleGroupField and ToggleGroupSetField.
 */
trait ToggleGroupFieldTrait
{
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
}
