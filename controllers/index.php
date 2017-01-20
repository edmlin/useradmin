<?php
class IndexController extends Controller
{
	function index($param)
	{
		$this->view='index';
	}
}
return new IndexController;