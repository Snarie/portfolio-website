<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="../public/js/script.js"></script>
    <script src="../public/js/navbar-toggle.js"></script>
    <link rel="stylesheet" href="../public/css/style.css">
    <link rel="stylesheet" href="../public/css/header.css">
    <title>Document</title>
</head>
<body>
<?php include path('layout', 'header.php');?>
<main>
	<?php include $viewPath;?>
</main>
<?php include path('layout', 'footer.php');?>
</body>
</html>