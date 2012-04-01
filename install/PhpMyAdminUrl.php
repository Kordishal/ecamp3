<?php

class PhpMyAdminUrl
	extends InstallerBase
{
	
	public function IsInstalled()
	{
		return isset($this->config->phpMyAdminUrl);
	}
	
	public function Install()
	{
		$path = $_REQUEST['phpMyAdminUrl'];
		$path = rtrim($path, '\/');
		$this->config->phpMyAdminUrl = $path . DIRECTORY_SEPARATOR;
	}
	
	
	public function RenderForm()
	{
		return 	"<lable>Url to local phpMyAdmin Website:</lable>&nbsp;" . 
				"<input type='text' name='phpMyAdminUrl' value='http://localhost/phpmyadmin' />";
	}
	
}