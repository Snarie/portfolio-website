<!DOCTYPE html>
<html>
<head>
	<?php include path('layout', 'head.php');?>
	<link rel="stylesheet" href="../public/css/form.css">
	<title>Document</title>
</head>
<body>
<?php include path('layout', 'header.php');?>
<main>
	<section class="form-container">
		<?php include $viewPath;?>
	</section>
</main>
<?php include path('layout', 'footer.php');?>
</body>
</html>