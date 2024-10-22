<!DOCTYPE html>
<html>
<head>
    <?php include path('layout', 'head.php');?>
    <title>Document</title>
</head>
<body>
<?php include path('layout', 'header.php');?>
<main class="grid gap-20">
	<?php include $viewPath;?>
</main>
<?php include path('layout', 'footer.php');?>
</body>
</html>