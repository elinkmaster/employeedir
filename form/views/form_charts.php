<!DOCTYPE html>
<html>
<head>
	<title>Form Builder | Form Chart</title>
	<link rel="icon" href="images/rm.png">
	<?php include 'stylesheets.php'; ?>
	<link rel="stylesheet" type="text/css" href="vendor/jqplot/jquery.jqplot.min.css" />
	<?php include 'scripts.php'; ?>
</head>
<body>

	<?php include 'header.php'; ?>

	<div class="panel">
		<div class="panel-header">
			<?php include 'navigation.php'; ?>
		</div>
		<div class="panel-body">
			<h3><?= htmlspecialchars($survey->survey_name) ?></h3>
			<?php $i = 1; ?>
			<?php foreach ($survey->questions as $question): ?>
				<div style="margin-bottom: 20px;" id="chart<?= $i ?>"></div>
				<?php ++$i; ?>
			<?php endforeach; ?>
		</div>
		<?php include 'footer.php'; ?>
	</div>

	<script type="text/javascript" src="vendor/jquery/js/jquery-migrate-1.1.1.min.js"></script>
	<script type="text/javascript" src="vendor/jqplot/jquery.jqplot.min.js"></script>
	<script type="text/javascript" src="vendor/jqplot/plugins/jqplot.categoryAxisRenderer.min.js"></script>
	<script type="text/javascript" src="vendor/jqplot/plugins/jqplot.barRenderer.min.js"></script>
	<script type="text/javascript" src="vendor/jqplot/plugins/jqplot.pointLabels.min.js"></script>
	<script type="text/javascript">
		$(function() {

			<?php $i = 1; ?>
			<?php foreach ($survey->questions as $question): ?>
				var line<?= $i ?> = <?= json_encode($question->choice_counts); ?>;
				var plot<?= $i ?> = $('#chart<?= $i ?>').jqplot([line<?= $i ?>], {
					title: <?= json_encode($question->question_text); ?>,
					seriesDefaults: {
						renderer: $.jqplot.BarRenderer,
						rendererOptions: {
							// Set the varyBarColor option to true to use different colors for each bar.
							// The default series colors are used.
							varyBarColor: true
						},
						pointLabels: {
							show: true
						}
					},
					axes:{
						xaxis: {
							renderer: $.jqplot.CategoryAxisRenderer
						},
						yaxis: {
							min: 0, 
							max: <?= ($question->max_answer_count+1) ?>
						}
					}
				});
			<?php ++$i; ?>
			<?php endforeach; ?>

		});
	</script>

</body>
</html>
