<?php

namespace App\Controllers;

class ProfileController extends Controller
{
	public function show(int $id)
	{
		return layout(view('profile/show'), ['id' => $id]);
		//return template('default.php', view('profile/show'), ['id' => $id]);
	}

}
