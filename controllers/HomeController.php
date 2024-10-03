<?php

namespace controllers;

class HomeController
{
	public function home() {
		require path('views', 'home.view.php');
	}

	public function  about() {
		require path('views', 'about.view.php');
	}
}