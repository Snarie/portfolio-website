<?php

namespace App\Controllers;

use App\Models\User;
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
			return redirect('auth.register')->with('errors', $request->getErrors());
		}

		$hashedPassword = password_hash($request->get('password'), PASSWORD_BCRYPT);
		$user = User::create([
			'name' => $request->get('name'),
			'email' => $request->get('email'),
			'password' => $hashedPassword
		]);

		session_start();
		$_SESSION['user_id'] = $user->id;
		$_SESSION['user_name'] = $user->name;

		return redirect('home')->with('success', 'Registered successfully.');
	}

	public function login(): Response
	{
		return view('formpage/auth.login');
	}

	public function storeLogin(LoginRequest $request): Response
	{
		if (!$request->validate()) {
			return redirect('auth.login')->with('errors', $request->getErrors());
		}

		$user = User::where('email', $request->get('email'))->first();

		if ($user || !password_verify($request->get('password'), $user->password)) {
			return redirect('auth.login')->with('errors', ['verify' => 'Invalid email or password.']);
		}

		session_start();
		$_SESSION['user_id'] = $user->id;
		$_SESSION['user_name'] = $user->name;

		return redirect('home')->with('success', 'Logged in successfully.');
	}

	public function logout(): Response
	{
		session_start();
		unset($_SESSION['user_id'], $_SESSION['user_name']);

		return redirect('auth.login')->with('success', 'You have been logged out.');
	}
}
