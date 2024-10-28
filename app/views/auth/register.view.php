<section>
	<article>
		<h1>Register</h1>
	</article>
</section>

<section class="form-container">
	<form action="/register" method="post" class="grid gap-20">
		<article>
			<div>
				<label for="email" class="f-18">Email</label>
				<input type="text" id="email" name="email" required value="<?=old('email')?>">
				<?=flashError('email')?>
			</div>
		</article>
		<article>
			<div>
				<label for="name" class="f-18">Name</label>
				<input type="text" id="name" name="name" required value="<?=old('name')?>">
				<?=flashError('name')?>
			</div>
		</article>
		<article>
			<div>
				<label for="password" class="f-18">Password</label>
				<input type="text" id="password" name="password" required value="<?=old('password')?>">
				<?=flashError('password')?>
			</div>
		</article>
		<article>
			<div>
				<label for="password_confirmation" class="f-18">Password confirmation</label>
				<input type="text" id="password_confirmation" name="password_confirmation" required value="<?=old('password_confirmation')?>">
				<?=flashError('password_confirmation')?>
			</div>
		</article>

		<div class="button-container">
			<button type="submit" class="button">Create Account</button>
		</div>
	</form>

</section>