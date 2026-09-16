<?php

namespace Wilr\ToggleGroupField\Tests\Forms;

use SilverStripe\Dev\TestOnly;
use SilverStripe\ORM\DataObject;

class ToggleGroupTestObject extends DataObject implements TestOnly
{
    private static $table_name = 'ToggleGroupTestObject';

    private static $db = [
        'Alignment' => 'Varchar',
        'Interests' => 'Varchar(255)',
    ];
}
