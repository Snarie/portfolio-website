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

<section class="title-container grid gap-30">
	<article class="mw-1024px">
		<h1 class="all-upper"><?=$project->name?></h1>
		<h2>
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
		</h2>
	</article>
	<article class="mw-1024px">
		<a href="<?=$project->github_link?>" class="large-button all-upper">project link</a>
	</article>
</section>

<section class="mw-1024px f-18">
	<article>
		<figure class="project-image-container">
			<img src="<?=$project->image_link?>" alt="Project Image" class="image">
			<img src="/public/images/laptop-overlay.png" alt="cover" class="cover">
		</figure>
	</article>
	<article class="j-left">
		<h3>Project description</h3>
		<p><?=nl2br($project->description)?></p>
	</article>
</section>

<section class="mw-1024px">
	<article class="mw-800px">
        <h3 style="text-align: left">Tools used</h3>
        <div>
	        <?php

	        foreach ($tools as $tool) {
		        echo "<p class='text-style-box'>$tool->name</p>";
	        }
	        ?>
        </div>
	</article>
</section>


