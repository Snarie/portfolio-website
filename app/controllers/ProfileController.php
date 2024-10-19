<?php

namespace App\Controllers;

use App\Responses\Response;

class ProfileController extends Controller
{
	public function show(int $id): Response
	{
		return view('profile.show', ['id' => $id]);
	}

}
