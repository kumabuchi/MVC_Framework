<?php

class TestController extends ControllerBase
{
    public function test()
    {   
	$title = "Welcome to MVC framework!";
	$this->set('title', $title);
    }

}
