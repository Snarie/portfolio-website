<?php

use App\Models\Project;

if (!isset($projects) || !is_array($projects)) {
	echo "Invalid data provided";
	return;
}

if ($user = auth()) {
    $admin = $user->admin;
} else {
    $admin = false;
}
?>

<section class="title-container">
	<article class="mw-1024px">
		<h1 class="all-upper">Projects</h1>
		<h2>Here you will find all created projects, with each project containing its own page</h2>
	</article>
</section>

<section class="mw-1024px">
	<?php
	if ($message = flash('success')) {
		echo "<article style=\"grid-column: span 2\">$message</article>";
	}
	if ($message = flash('fail')) {
		echo "<article style=\"grid-column: span 2\">$message</article>";
	}
	?>
    <form action="/projects/filter" method="post">
        <article>
            <div>
                <label for="tool" class="f-18" style="color: var(--text-color)">Filter on Tools</label>
                <select id="tool" name="tool" class="skills-select">
				    <?php
				    $selectedTool = (int)old('tool');
				    foreach (\App\Models\Tool::all() as $tool) {
                        $isSelected = $tool->id === $selectedTool ? "selected" : "";
					    echo "<option value=\"".$tool->id."\" $isSelected>".htmlspecialchars($tool->name)."</option>";
				    }
				    ?>
                </select>
            </div>
            <button type="submit" class="button">Filter</button>
        </article>
        <div class="button-container">
        </div>
    </form>
</section>

<?php
foreach ($projects as $project):
	if ($project instanceof Project): ?>
        <section class="mw-1600px f-18 grid column-2 decrease-columns">
            <article>
                <figure class="project-image-container">
                    <img src="<?=$project->image_link?>" alt="Project Image" class="image">
                    <img src="/public/images/laptop-overlay.png" alt="cover" class="cover">
                </figure>
            </article>
            <article class="v-center">
                <div class="grid gap-20" style="height: 80%">
                    <h2 style="text-align: left"><?=$project->name?></h2>
                    <p style="text-align: left"><?=$project->description?></p>
                    <div>
                        <a href="/projects/<?=$project->id?>" class="button">project link</a>
	                    <?php if ($admin): ?>
                            <a href="/projects/<?=$project->id?>/edit" class="button">edit project</a>
	                    <?php endif; ?>
                    </div>
                </div>

            </article>
        </section>
	<?php endif;
endforeach;
?>
