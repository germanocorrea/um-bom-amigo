<?php
/**
 * Created by PhpStorm.
 * User: ggcorrea
 * Date: 13/12/16
 * Time: 08:33
 */

use PHPUnit_Framework_TestCase as PHPUnit;
use Controller\User;

class ControllerUserTest extends PHPUnit
{
    protected $user;

    public function setUp()
    {
        $this->user = new User();
    }
}