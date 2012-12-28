<?php

class TestController extends ControllerBase
{
    public function test()
    {        
	$tmp = "HELLO WORLD FROM CONTROLLER";
	$tmp2 = $this->model->getAll();
	$this->set('tmp',$tmp);
	$this->set('tmp2',$tmp2);
    }

}
