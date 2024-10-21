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
?>

<section class="mw-1024px title-container grid gap-30">
	<article>
		<h1 class="all-upper"><?=$project->name?></h1>
		<h3>
			<?php
			$startDate = date('d F Y', strtotime($project->start_date));
			echo "$startDate - ";
			if ($project->end_date != null) {
				$endDate = date('d F Y', strtotime($project->end_date));
				echo "$endDate";
			} else {
				echo "ongoing";
			}
			?>
		<h3/>
	</article>
	<article>
		<a href="<?=$project->github_link?>" class="large-button all-upper">project link</a>
	</article>
</section>
<section>
	<article>
		<figure class="project-image-container">
			<img src="/<?=$project->image_link?>" alt="Project Image" class="image">
			<img src="/public/images/laptop-overlay.png" alt="cover" class="cover">
		</figure>
	</article>
	<article>
		<h3>Project description</h3>
		<p><?=nl2br($project->description)?></p>
	</article>
</section>
<section>
	<article>
		<?php
		foreach ($tools as $tool) {
			echo "<p>$tool->name</p>";
		}
		?>
	</article>
</section>


