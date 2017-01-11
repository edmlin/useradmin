<?php
class IndexController
{
	static function handle($action)
	{
		$view=include "views/index.php";
		$view::render();
	}
}
return 'IndexController';