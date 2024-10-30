<section>

</section>

<section class="grid gap-10 column-2">
    <article>
        <p class="f-24">Pages</p>
        <a class="footer-link" href="/">Home</a>
        <a class="footer-link" href="/about">About</a>
        <a class="footer-link" href="/projects">Projects</a>
    </article>
    <article>
        <p class="f-24">Latest Projects</p>
            <?php
                $latestProjects = \App\Models\Project::latestProjects();
                foreach($latestProjects as $project) {
                    echo "<a class=\"footer-link\" href=\"/projects/$project->id\">$project->name</a>";
                }
            ?>
    </article>
</section>

<section>
    <p>Contact me</p>
    <br>
    <a class="footer-icon" href="https://github.com/Snarie"><img src="/public/images/github-icon.png" alt=""></a>
    <a class="footer-icon" href="mailto:barryvandenberg@gmail.com"><img src="/public/images/mail-icon.png" alt=""></a>
    <br><br>
    <a class="footer-link" href="/public/CV-Resume.pdf">Download cv</a>
</section>

<section style="grid-column: span 3">
    <br><p>© Copyright 2024 Barry van den Berg</p>
</section>