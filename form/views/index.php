<!DOCTYPE html>
<html>
<head>
	<title>Form Builder | Home</title>
	<link rel="icon" href="images/rm.png">
	<?php include 'stylesheets.php'; ?>
	<?php include 'scripts.php'; ?>
</head>
<body>
	<?php include 'header.php'; ?>

	<div class="panel">
		<div class="panel-header">
			<?php include 'navigation.php'; ?>
		</div>
		<div class="panel-body">
			<h2>Welcome <?= $user->first_name, ' ', $user->last_name; ?></h2>
		</div>
		<?php include 'footer.php'; ?>
	</div>

</body>
</html>
