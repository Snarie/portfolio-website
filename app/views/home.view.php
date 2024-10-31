<section class="title-container grid gap-30">
	<article class="mw-1024px">
		<h5>Hey, i am</h5>
		<h1 class="all-upper">Barry van den Berg</h1>
		<h2>A software develop student.</h2>

	</article>
</section>

<section class="mw-1600px">
    <article>
        <h2>New Projects: </h2>
    </article>
    <article class="carousel">
        <div class="carousel-images">
	        <?php
	        $projects = \App\Models\Project::latestProjects(4);
	        foreach($projects as $project): ?>
                <div class="carousel-item">
                    <a href="/projects/<?=$project->id?>">
                        <figure class="project-image-container">
                            <img src="<?=$project->image_link?>" alt="Project Image" class="image">
                            <img src="/public/images/laptop-overlay.png" alt="cover" class="cover">
                        </figure>
                        <h2 style="color: var(--text-color)"><?=$project->name?></h2>
                    </a>
                </div>
	        <?php endforeach; ?>
        </div>

        <div class="carousel-dots">
            <span class="dot active"></span>
            <span class="dot"></span>
            <span class="dot"></span>
            <span class="dot"></span>
        </div>
    </article>

</section>

<section class="mw-1024px">
    <article class="mw-800px">
        <h3 style="text-align: left">Skills</h3>
        <div>
			<?php

			foreach (\App\Models\Tool::all() as $tool) {
				echo "<p class='text-style-box'>$tool->name</p>";
			}
			?>
        </div>
    </article>
</section>

<script src="/public/js/carousel-toggle.js"></script>