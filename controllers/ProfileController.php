<?php

namespace controllers;

class ProfileController
{
	public function show(int $id): bool
	{
		return template('default.php', view('profile/show.view.php'), ['id' => $id]);
	}

}
