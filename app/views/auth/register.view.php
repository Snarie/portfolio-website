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
				<label for="username" class="f-18">Username</label>
				<input type="text" id="username" name="username" required value="<?=old('username')?>">
				<?=flashError('username')?>
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
				<label for="password_confirm" class="f-18">Password confirm</label>
				<input type="text" id="password_confirm" name="password_confirm" required value="<?=old('password_confirm')?>">
				<?=flashError('password_confirm')?>
			</div>
		</article>

		<div class="button-container">
			<button type="submit" class="button">Create Account</button>
		</div>
	</form>

</section>