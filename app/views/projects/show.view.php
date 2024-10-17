<?php

use App\Models\Project;

if (!isset($project) || !$project instanceof Project) {
	echo "Invalid data provided";
	return;
}
if (!isset($tools) || !is_array($tools)) {
	echo "Invalid data provided";
	return;
}

foreach ($tools as $tool) {
	echo "$tool->name<br>";
}
echo "id: $project->id<br>";
echo "description: $project->description<br>";
echo "start: $project->start_date<br>";
echo "end: $project->end_date<br>";
echo "<img src=\"/$project->image_link\" alt=\"Project Image\"><br>";
