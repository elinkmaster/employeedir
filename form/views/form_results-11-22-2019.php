<!DOCTYPE html>
<html>
<head>
	<title>Form Builder | Form Result</title>
	<link rel="icon" href="images/eLink_logo.png">
	<?php include 'stylesheets.php'; ?>
	<?php include 'scripts.php'; ?>
</head>
<body>

	<?php include 'header.php'; ?>

	<div class="panel">
		<div class="panel-header">
			<?php include 'navigation.php'; ?>

			<div style="float: right; padding: 15px;">
				<a class="btn btn-primary view_charts" href="form_charts.php?form_id=<?php echo htmlspecialchars($survey->survey_id); ?>">View Charts</a>
				<a class="btn btn-primary download_csv" href="form_results.php?form_id=<?php echo htmlspecialchars($survey->survey_id); ?>&amp;action=download_csv">Download CSV</a>
			</div>
		</div>
		<div class="panel-body">
			<h3><?= htmlspecialchars($survey->survey_name); ?></h3>
			<br>
			<table class="table" id="table_results">
				<thead>
					<tr>
						<th>#</th>
						<?php foreach ($survey->questions as $question): ?>
							<th><?php echo htmlspecialchars($question->question_text); ?></th>
						<?php endforeach; ?>
						<th>Time Taken</th>
					</tr>
				</thead>
				<tbody>
					<?php if (empty($survey->responses)): ?>
						<tr>
							<td colspan="<?php echo count($survey->questions)+2; ?>"><em>No results</em></td>
						</tr>
						<?php else: ?>
						<?php foreach ($survey->responses as $key => $response): ?>
							<tr>
								<td><?= $key+1 ?>.</td>
								<?php foreach ($survey->questions as $question): ?>
									<td><?php $field = 'question_' . htmlspecialchars($question->question_id); echo htmlspecialchars(substr($response->$field,0,50)); ?></td>
								<?php endforeach; ?>
								<td class="text-center">
									<?php
										$date = new DateTime($response->time_taken, new DateTimeZone('GMT'));
										$date->setTimezone(new DateTimeZone('Asia/Manila'));
										echo $date->format('M d, Y h:i A');
									?>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
		<?php include 'footer.php'; ?>
	</div>

	<script type="text/javascript">
		$(function() {
			$('.download_csv').button({ icons: { primary: 'ui-icon-document' } });
			$('.view_charts').button({ icons: { primary: 'ui-icon-image' } });
		});
	</script>
	
</body>
</html>
