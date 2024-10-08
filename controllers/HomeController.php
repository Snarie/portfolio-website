<?php

namespace controllers;

class HomeController
{
	public function home(): bool
	{
		return template('default.php', view('home.view.php'));
	}

	public function  about(): bool
	{
		return template('default.php', view('about.view.php'));
	}
}