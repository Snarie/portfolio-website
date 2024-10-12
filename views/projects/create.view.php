<H2>Create a New Project</H2>

<form action="/projects" method="post">
	<div>
		<label for="name">Project Name</label>
		<input type="text" id="name" name="name" required>
	</div>

	<div>
		<label for="description">Project Description</label>
		<textarea id="description" name="description" required></textarea>
	</div>

	<div>
		<label for="start_date">Start Date</label>
		<input type="date" id="start_date" name="start_date" required>
	</div>

	<div>
		<label for="end_date">End Date</label>
		<input type="date" id="end_date" name="end_date">
	</div>

	<div>
		<label for="tools">Tools Used</label>
		<select id="tools" name="tools[]" multiple>
			<?php
			foreach ($tools as $tool) {
				echo "<option value=\"".$tool['id']."\">".htmlspecialchars($tool['name'])."</option>";
			}
			?>
		</select>
	</div>

	<div>
		<button type="sumbit">Create Project</button>
	</div>
</form>