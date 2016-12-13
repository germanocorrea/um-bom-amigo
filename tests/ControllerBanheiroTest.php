<?php
/**
 * Created by PhpStorm.
 * User: ggcorrea
 * Date: 13/12/16
 * Time: 08:33
 */

use PHPUnit_Framework_TestCase as PHPUnit;
use Controller\Banheiro;

class ControllerBanheiroTest extends PHPUnit
{
    protected $banheiro;

    public function setUp()
    {
        $this->banheiro = new Banheiro();
    }
}