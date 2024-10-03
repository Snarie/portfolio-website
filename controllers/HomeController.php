<?php

namespace controllers;

class HomeController
{
	public function home() {
		return template('default.php', view('home.view.php'));
	}

	public function  about() {
		return template('default.php', view('about.view.php'));
	}
}