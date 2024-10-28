<section>
	<article>
		<h1>Login</h1>
	</article>
</section>

<section class="form-container">
	<form action="/login" method="post" class="grid gap-20">
        <article>
            <div>
                <label for="email" class="f-18">Email</label>
                <input type="text" id="email" name="email" required value="<?=old('email')?>">
				<?=flashError('email')?>
            </div>
        </article>
        <article>
            <div>
                <label for="password" class="f-18">Password</label>
                <input type="text" id="password" name="password" required value="<?=old('password')?>">
				<?=flashError('password')?>
            </div>
        </article>

		<div class="button-container">
			<button type="submit" class="button">Login</button>
		</div>
        <?=flashError('verify')?>
	</form>

</section>