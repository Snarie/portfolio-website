<?php

namespace controllers;

class ProjectController
{
	public function index(): bool
	{
		return template('default.php', view('projects/index.view.php'));
	}

	public function create(): bool
	{
		return template('default.php', view('projects/create.view.php'));
	}

	public function store(): bool
	{
		return $this->index();
	}

	public function show(int $id): bool
	{
		return template('default.php', view('projects/show.view.php'), ['id' => $id]);
	}

	public function edit(int $id): bool
	{
		return template('default.php', view('projects/edit.view.php'), ['id' => $id]);
	}

	public function update(int $id): bool
	{
		return $this->show($id);
	}

	public function destroy(int $id): bool
	{
		return $this->index();
	}

}