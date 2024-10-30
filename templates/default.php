<!DOCTYPE html>
<html>
<head>
    <?php include path('layout', 'head.php');?>
    <title>Document</title>
</head>
<body>
<?php include path('layout', 'header.php');?>
<main class="grid gap-50">
	<?php include $viewPath;?>
</main>
<footer>
	<?php include path('layout', 'footer.php');?>
</footer>
</body>
</html>