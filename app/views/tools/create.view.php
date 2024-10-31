<section>
	<article>
		<h1>Create a New Tool</h1>
	</article>
</section>

<section class="form-container">
	<form action="/tools" method="post" class="grid gap-20">

		<?php if ($message = flash('success')): ?>
            <article>
				<?=$message?>
            </article>
		<?php endif;?>

		<?php if ($message = flashError('auth')): ?>
			<article>
				<?=$message?>
			</article>
		<?php endif;?>

		<article>
			<div>
				<label for="name" class="f-18">Tool Name (required)</label>
				<input type="text" id="name" name="name" required value="<?=old('name')?>">
				<?=flashError('name')?>
			</div>
		</article>

		<div class="button-container">
			<button type="submit" class="button">Create Tool</button>
		</div>
	</form>

</section>