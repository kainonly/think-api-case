<?php

namespace tests;

use PHPUnit\Framework\TestCase;
use think\App;

class BaseTest extends TestCase
{
    /**
     * @var App
     */
    protected $app;

    public function setUp(): void
    {
        parent::setUp();
        $this->app = new App();
        $this->app->initialize();
    }
}