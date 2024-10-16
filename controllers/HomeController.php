<?php

namespace controllers;

class HomeController extends Controller
{
	public function home(): bool
	{
		return layout(view('home'));
	}

	public function  about(): bool
	{
		return layout(view('about'));
	}
}