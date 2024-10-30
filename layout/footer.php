<section class="grid gap-30 column-3 decrease-columns ">
    <article class="grid gap-10 column-2 j-left">
        <p class="f-24" style="grid-column: span 2;">Pages</p>
        <a class="footer-link" href="/">Home</a>
        <a class="footer-link" href="/about">About</a>
        <a class="footer-link" href="/projects">Projects</a>
    </article>

    <article class="grid gap-10 column-2 j-left">
        <p class="f-24" style="grid-column: span 2">Latest Projects</p>
	    <?php
	    $latestProjects = \App\Models\Project::latestProjects(4);
	    foreach($latestProjects as $project) {
		    echo "<a class=\"footer-link\" href=\"/projects/$project->id\">$project->name</a>";
	    }
	    ?>
    </article>

    <article class="grid j-left">
        <p class="f-24">Contact me</p>
        <div>
            <a class="footer-icon" href="https://github.com/Snarie"><img src="/public/images/github-icon.png" alt=""></a>
            <a class="footer-icon" href="mailto:barryvandenberg@gmail.com"><img src="/public/images/mail-icon.png" alt=""></a>
        </div>
        <a class="footer-link" href="/public/CV-Resume.pdf">Download Resume</a>
    </article>

</section>

<section>
    <br><p>© Copyright 2024 Barry van den Berg</p>
</section>