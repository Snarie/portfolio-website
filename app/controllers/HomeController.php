<?php

namespace App\Controllers;

class HomeController extends Controller
{
	public function home()
	{
		return layout(view('home'));
	}

	public function  about()
	{
		return layout(view('about'));
	}
}