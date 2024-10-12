<H1>Create a New Project</H1>

<form action="/projects" method="post" class="grid gap-20">

    <article class="grid gap-10">
        <div>
            <label for="name" class="f-18">Project Name</label>
            <input type="text" id="name" name="name" required>
        </div>
    </article>
    <article class="grid gap-10">
        <div>
            <label for="description" class="f-18">Project Description</label>
            <textarea id="description" name="description" required></textarea>
        </div>
    </article>
    <article class="grid column-2">
        <div class="c-span-2">
            <label for="disable_end_date" class="f-14">
                <input type="checkbox" id="disable_end_date" name="disable_end_date" checked="false">
                Disable End Date
            </label>
        </div>
        <div>
            <label for="start_date" class="f-18">Start Date</label>
            <input type="date" id="start_date" name="start_date" required>
        </div>

        <div>
            <label for="end_date" class="f-18">End Date</label>
            <input type="date" id="end_date" name="end_date">
        </div>
    </article>
	<article class="grid gap-10">
        <div>
            <label for="tools" class="f-18">Tools Used</label>
            <select id="tools" name="tools[]" multiple>
				<?php
				foreach ($tools as $tool) {
					echo "<option value=\"".$tool['id']."\">".htmlspecialchars($tool['name'])."</option>";
				}
				?>
            </select>
        </div>
    </article>

	<div class="button-container">
		<button type="sumbit">Create Project</button>
	</div>

</form>

<script src="/public/js/disable-end-date.js"></script>