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
            <ul style="background-color: green">
                <li><button onclick="applyDarkMode()" class="navbar-button-dark">Dark Mode</button></li>
                <li><button onclick="applyLightMode()" class="navbar-button-light">Light Mode</button></li>
                <li><button onclick="resetToSystemDefault()" class="navbar-button-theme-reset">System Default</button></li>
            </ul>
            <ul style="background-color: blue">
                <li><a href="/">Home</a></li>
                <li><a href="/about">About</a></li>
                <li><a href="/projects">Projects</a></li>
                <li><a href="/projects/create">Create</a></li>
            </ul>
            <ul style="background-color: yellow">
                
            </ul>
        </section>

	</nav>
</header>