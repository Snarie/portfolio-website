<?php
if (!isset($tools) || !is_array($tools)) {
	echo "Invalid data provided";
	return;
}
?>
<section>
    <article>
        <h1>Manage Tools</h1>
    </article>
</section>

<section class="mw-1024px">
    <form action="/tools" method="post" class="grid gap-20">
        <article>
            <div>
                <label for="name" class="f-18">Tool Name (required)</label>
                <input type="text" id="name" name="name" required value="<?=old('name')?>">
				<?=flashError('name')?>
            </div>
            <div class="button-container">
                <button type="submit" class="button">Create Tool</button>
            </div>
        </article>
    </form>
</section>

<section class="mw-1024px grid gap-10 column-2">
    <?php
    if ($message = flash('success')) {
	    echo "<article style=\"grid-column: span 2\">$message</article>";
    }
    if ($message = flash('fail')) {
	    echo "<article style=\"grid-column: span 2\">$message</article>";
    }
    ?>

    <?php
	/** @var \App\Models\Tool $tool */
	foreach($tools as $tool): ?>
        <article class="no-line" style="display: flex; justify-content: left">
            <form action="/tools/<?=$tool->id?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="_method" value="PUT">
                <article class="no-line" style="display: flex; justify-content: space-between">
                    <div>
                        <label for="name" class="f-18"></label>
                        <input type="text" id="name" name="name" required value="<?=$tool->name?>" style="width: auto">
                    </div>
                    <div class="button-container j-right">
                        <button type="submit" class="button">Update</button>
                    </div>
                </article>
            </form>
            <form action="/tools/<?=$tool->id?>/delete" method="post">
                <input type="hidden" name="_method" value="DELETE">
                <div class="button-container">
                    <button type="submit" class="button">Delete</button>
                </div>
            </form>
        </article>

	<?php endforeach;?>
</section>