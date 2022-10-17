<!DOCTYPE html>
<html>
<head>
	<title>Thank You</title>
	<link rel="icon" href="images/rm.png">
	<?php include 'stylesheets.php'; ?>
	<?php include 'scripts.php'; ?>
</head>
<body>

	<?php $title = htmlspecialchars($survey->survey_name); ?>
	<?php include 'public_header.php'; ?>

	<div class="panel">
		<div class="panel-header">
			<div class="panel-title-public">FORM RESPONSE SUBMITTED</div>
			<?php if (isset($statusMessage)): ?>
				<p class="bg-success"><?= htmlspecialchars($statusMessage); ?></p>
			<?php endif; ?>
		</div>
		<div class="panel-body">
			<h3>Thank you for completing the form!</h3>
			<p>Thank you for taking the time to complete the form. Your feedback is very valuable to us.</p>
		</div>
		<?php include 'footer.php'; ?>
	</div>

</body>
</html>
