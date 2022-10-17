<!DOCTYPE html>
<html>
<head>
	<title>Form Builder | Forms</title>
	<link rel="icon" href="images/rm.png">
	<?php include 'stylesheets.php'; ?>
	<?php include 'scripts.php'; ?>
</head>
<body>

	<?php include 'header.php'; ?>

	<div class="panel">
		<div class="panel-header">
			<?php include 'navigation.php'; ?>

			<div class="status-msg-bar">
				<?php if (isset($statusMessage)): ?>
					<p class="bg-success"><?= htmlspecialchars($statusMessage); ?></p>
				<?php endif; ?>
			</div>
		</div>
		<div class="panel-body">
			<h3>Forms</h3>
			<br>
			<table class="table">
				<thead>
					<tr>
						<th>Form Title</th>
						<th>Manage Form</th>
						<th>Form URL</th>
						<th>Results</th>
						<th>Charts</th>
						<th>Created By</th>
					</tr>
				</thead>
				<tbody>
					<?php if (empty($surveys)): ?>
					<tr>
						<td colspan="5"><em>No forms</em></td>
					</tr>
					<?php endif; ?>
					<?php foreach ($surveys as $survey): ?>
						<tr>
							<td><?= htmlspecialchars($survey->survey_name); ?></td>
							<td class="text-center"><a class="btn btn-primary edit_survey" href="form_edit.php?form_id=<?= htmlspecialchars($survey->survey_id); ?>">Edit</a></td>
                                                        <td class="text-center"><button class="btn btn-primary" id="btnCopyURL" data-clipboard-text="<?= $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'] ?>/form_response.php?form_title=<?= urlencode($survey->survey_name) ?>">Copy URL</button>
							<td class="text-center"><a class="btn btn-primary take_survey" href="form_results.php?form_id=<?= htmlspecialchars($survey->survey_id); ?>">View</a></td>
							<td class="text-center"><a class="btn btn-primary view_charts" href="form_charts.php?form_id=<?= htmlspecialchars($survey->survey_id); ?>">View</a></td>
							<td class="text-center">
								<?php
									echo $survey->created_by_name.'<br>';
									if($survey->created_at != null) {
										$date = new DateTime($survey->created_at, new DateTimeZone('GMT'));
										$date->setTimezone(new DateTimeZone('Asia/Manila'));
										echo '<small>'.$date->format('M d, Y h:i A').'</small>';
									}
								?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<br>
			<a class="btn btn-primary" id="add_survey_button" href="form_edit.php">Add Form</a>
		</div>
		<?php include 'footer.php'; ?>
	</div>

	<script type="text/javascript">
		$(function() {

			$('#add_survey_button').button();
			$('.edit_survey').button();
			$('.view_charts').button();

			var clipboard = new ClipboardJS('#btnCopyURL');
			clipboard.on('success', function(e) {
			    $('.status-msg-bar').html('<p class="bg-success">Copied</p>');
			    e.clearSelection();
			});

		});

	</script>

</body>
</html>
