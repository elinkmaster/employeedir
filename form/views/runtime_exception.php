<!DOCTYPE html>
<html>
<head>
	<title>Error</title>
	<?php include 'stylesheets.php'; ?>
	<?php include 'scripts.php'; ?>
</head>
<body>
	<div id="main">
		<?php include 'header.php'; ?>
		<div id="site_content">
			<h1>Runtime Error</h1>
			<div id="content">
				<div id="error_dialog" title="Error" style="display: none;">
					<p id="statusMessage" class="error"><?= l2br(htmlspecialchars($statusMessage)) ?></p>
				</div>
			</div>
		</div>
		<?php include 'footer.php'; ?>
	</div>

	<script type="text/javascript">
		$(function() {
			showErrorDialog();
		});

		function showErrorDialog() {
			var buttonsObj = {};
			buttonsObj['OK'] = function() {
				window.location.reload(true);
			}

			$('#error_dialog').dialog({
				width: 420,
				buttons: buttonsObj
			});
		}
	</script>

</body>
</html>
