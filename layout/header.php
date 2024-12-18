<header>
	<div class="navbar-logo">
		<p>
            <?php
            session_start();
            if (isset($_SESSION['user_id']))
            {
                echo $_SESSION['user_name'];
            }
            ?>
        </p>
	</div>
    <div class="navbar-button">
        <div></div>
        <div></div>
        <div></div>
    </div>
	<nav class="navbar-fullscreen grid column-3">
        <section class="grid column-3" style="width: 80%">
            <ul>
                <li><button onclick="applyDarkMode()" class="navbar-button-dark">Dark Mode</button></li>
                <li><button onclick="applyLightMode()" class="navbar-button-light">Light Mode</button></li>
                <li><button onclick="resetToSystemDefault()" class="navbar-button-theme-reset">System Default</button></li>
            </ul>
            <ul>
                <li><a href="/">Home</a></li>
                <li><a href="/about">About</a></li>
                <li><a href="/projects">Projects</a></li>
            </ul>
            <ul>
                <?php if (auth()): ?>
                    <li>
                        <a>
                            <form action="/logout" method="post">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit">Logout</button>
                            </form>
                        </a>
                    </li>
                    <?php if (auth()->admin): ?>
                        <li><a href="/projects/create">Create Project</a></li>
                        <li><a href="/tools">Edit Tools</a></li>
                    <?php endif; ?>
                <?php else: ?>
                    <li><a href="/login">Login</a></li>
                    <li><a href="/register">Register</a></li>
                <?php endif; ?>
            </ul>
        </section>

	</nav>
</header>