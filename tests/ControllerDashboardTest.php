<?php
/**
 * Created by PhpStorm.
 * User: ggcorrea
 * Date: 13/12/16
 * Time: 08:33
 */

use PHPUnit_Framework_TestCase as PHPUnit;
use Controller\Dashboard;

class ControllerDashboardTest extends PHPUnit
{
    protected $dashboard;

    public function setUp()
    {
        $this->dashboard = new Dashboard();
    }
}