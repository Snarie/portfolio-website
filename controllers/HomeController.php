<?php

namespace controllers;

class HomeController
{
	public function home(): bool
	{
		return route(view('home'));
	}

	public function  about(): bool
	{
		return route(view('about'));
	}
}