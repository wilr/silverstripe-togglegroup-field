<?php

use SilverStripe\CMS\Controllers\ContentController;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Dev\TestOnly;

// silverstripe/cms expects the consuming project to define top level "Page"
// and "PageController" classes (e.g. SilverStripe\CMS\Model\RedirectorPage
// extends Page). This module has no CMS recipe of its own, so provide
// minimal stand-ins only for the test run - a real project will always
// define its own Page/PageController classes.
if (!class_exists('Page') && class_exists(SiteTree::class)) {
    class Page extends SiteTree implements TestOnly
    {
        private static $table_name = 'ToggleGroupField_Page';
    }
}

if (!class_exists('PageController') && class_exists(ContentController::class)) {
    class PageController extends ContentController implements TestOnly
    {
    }
}
