<?php

namespace controllers;

class ProfileController extends Controller
{
	public function show(int $id): bool
	{
		return layout(view('profile/show'), ['id' => $id]);
		//return template('default.php', view('profile/show'), ['id' => $id]);
	}

}
