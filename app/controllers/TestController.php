<?php

class TestController extends ControllerBase
{
    public function test()
    {        
	$tmp2 = $this->model->getAll();
	$this->set('tmp',$this->ini['site']['title']);
	$this->set('tmp2',$tmp2);
    }

}
