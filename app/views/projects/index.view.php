<?php

use App\Models\Project;

if (!isset($projects) || !is_array($projects)) {
	echo "Invalid data provided";
	return;
}
?>

<section>
	<article>
		<h1 class="all-upper">Projects</h1>
		<h3>Here you will find all created projects, with each project containing its own page</h3>
	</article>
</section>

    <?php
    foreach ($projects as $project) {
	    if ($project instanceof Project) { ?>
            <section class="grid column-2 gap-10 mw-1400px">
                <article>
                    <figure class="project-image-container">
			            <img src="/<?=$project->image_link?>" alt="Project Image" class="image">
                        <img src="/public/images/laptop-overlay.png" alt="cover" class="cover">
                    </figure>
                </article>
                <article style="align-items: center">
                    <div class="grid gap-20">
                        <h2 style="text-align: left"><?=$project->name?></h2>
                        <h3 style="text-align: left"><?=$project->description?><br> wefwe<br> wewefw</h3>
                        <!--TODO: create redirect to project itself-->
                        <a href="/projects/<?=$project->id?>" class="button">project link</a>
                    </div>
                </article>
            </section><?php
        }
    }
    ?>
</section>
