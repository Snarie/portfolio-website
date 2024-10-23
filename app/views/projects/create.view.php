<?php
if (!isset($tools) || !is_array($tools)) {
	echo "Invalid data provided";
	return;
}
?>

<section>
    <article>
        <h1>Create a New Project</h1>
    </article>
</section>

<section class="form-container">
    <form action="/projects" method="post" class="grid gap-20">

        <article>
            <div>
                <label for="name" class="f-18">Project Name</label>
                <input type="text" id="name" name="name" required>
            </div>
        </article>

        <article>
            <div>
                <label for="github_link" class="f-18">Project Link</label>
                <input type="text" id="github_link" name="github_link" required>
            </div>
        </article>

        <article>
            <div>
                <label for="image" class="f-18">Select a Project Image (required)</label>
                <input type="file" id="imageInput" accept="image/*" required>
                <button type="button" onclick="openImagePopup()" class="button">Add Photo</button>
            </div>
            <div id="imagePopup" style="display: none;">
                <canvas id="imageCanvas"></canvas>
                <button type="button" onclick="cropImage()" class="button">Crop Image</button>
            </div>
            <div>
                <input type="hidden" name="cropped_image" id="croppedImageInput">
            </div>
        </article>

        <article>
            <div>
                <label for="description" class="f-18">Project Description</label>
                <textarea id="description" name="description" required></textarea>
            </div>
        </article>

        <article class="grid column-2 gap-10">
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

        <article>
            <div>
                <label for="tools" class="f-18">Tools Used</label>
                <select id="tools" name="tools[]" multiple>
					<?php
					foreach ($tools as $tool) {
						echo "<option value=\"".$tool->id."\">".htmlspecialchars($tool->name)."</option>";
					}
					?>
                </select>
            </div>
        </article>

        <div class="button-container">
            <button type="submit" class="button">Create Project</button>
        </div>

    </form>

</section>

<script src="/public/js/disable-end-date.js"></script>
<script src="/public/js/image-cropper.js"></script>