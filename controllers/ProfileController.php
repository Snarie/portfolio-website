<?php

namespace controllers;

class ProfileController
{
	public function show(int $id): bool
	{
		return route(view('profile/show'), ['id' => $id]);
		//return template('default.php', view('profile/show'), ['id' => $id]);
	}

}
