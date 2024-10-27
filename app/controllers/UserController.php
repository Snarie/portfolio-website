<?php

namespace App\Controllers;

use App\Requests\LoginRequest;
use App\Requests\RegisterRequest;
use App\Responses\ErrorResponse;
use App\Responses\Response;

class UserController extends Controller
{
	// required pages:
	// * login
	// * register
	// * update page (includes delete)


	public function register(): Response
	{
		return view('auth.register');
	}

	public function storeRegister(RegisterRequest $request): Response
	{
		if (!$request->validate()) {
			return redirect('auth.register')->with('errors', $request->getErrors());
		}

		//TODO: create User, then login user
		return redirect('home')->with('success', 'Registered successfully.');
	}

	public function login(): Response
	{
		return view('auth.login');
	}

	public function loginRegister(LoginRequest $request): Response
	{
		if (!$request->validate()) {
			return redirect('auth.login')->with('errors', $request->getErrors());
		}

		//TODO: login user
		return redirect('home')->with('success', 'Logged in successfully.');
	}
}
