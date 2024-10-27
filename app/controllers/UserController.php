<?php

namespace App\Controllers;

use App\Requests\LoginRequest;
use App\Requests\RegisterRequest;
use App\Responses\Response;

class UserController extends Controller
{
	public function register(): Response
	{
		return view('formpage/auth.register');
	}

	public function storeRegister(RegisterRequest $request): Response
	{
		if (!$request->validate()) {
			return redirect('formpage/auth.register')->with('errors', $request->getErrors());
		}

		//TODO: create User, then login user
		return redirect('home')->with('success', 'Registered successfully.');
	}

	public function login(): Response
	{
		return view('formpage/auth.login');
	}

	public function storeLogin(LoginRequest $request): Response
	{
		if (!$request->validate()) {
			return redirect('formpage/auth.login')->with('errors', $request->getErrors());
		}

		//TODO: login user
		return redirect('home')->with('success', 'Logged in successfully.');
	}
}
