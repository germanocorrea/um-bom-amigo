<?php
/**
 * Created by PhpStorm.
 * User: ggcorrea
 * Date: 13/12/16
 * Time: 08:33
 */

use PHPUnit_Framework_TestCase as PHPUnit;
use Controller\Index;

class ControllerIndexTest extends PHPUnit
{
    protected $index;

    public function setUp()
    {
        $this->index = new Index();
    }
}