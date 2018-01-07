<!DOCTYPE html>
<html lang="cs">
	<head>
		<title><?= $header['title'] ?></title>
		<meta charset="UTF-8">
		<meta name="author" content="<?= $header['author'] ?>">
		<meta name="description" content="<?= $header['description'] ?>">
		<meta name="keywords" content="<?= $header['keywords'] ?>">
		<meta name="robots" content="noindex, nofollow">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
		<link rel="stylesheet" type="text/css" href="/styles/style.css">
		<?php if (!empty($this->controller->addStyles)): ?>
		<?php foreach($this->controller->addStyles as $addStyle): ?>
		<link rel="stylesheet" type="text/css" href="/styles/<?= $addStyle ?>">
		<?php endforeach; ?>
		<?php endif; ?> 
	</head>
	<body>

	<?php $this->controller->render(); ?>

	<!-- Scripts -->
		<?php if (!empty($this->controller->addScripts)): ?>
		<?php foreach($this->controller->addScripts as $addScript): ?> 
		<script type="text/javascript" src="/scripts/<?= $addScript ?>"></script>
		<?php endforeach; ?>
		<?php endif; ?> 		
	<!-- End scripts -->
	</body>
</html>