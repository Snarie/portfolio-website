<?php

namespace App\Controllers;

use App\Responses\Response;

class HomeController extends Controller
{
	public function home(): Response
	{
		return view('home');
	}

	public function  about(): Response
	{
		return view('about');
	}
}