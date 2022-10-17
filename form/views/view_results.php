<!DOCTYPE html>
<html>
<head>
	<title>Form Builder | Form Result</title>
	<link rel="icon" href="images/rm.png">
	<?php include 'stylesheets.php'; ?>
</head>
<body>
	<div class="panel">
		<div class="panel-body">
			<h3><?= htmlspecialchars($survey[0]->survey_name); ?></h3>
			<br>
			<?php
			foreach($survey as $res):
			?>
			<div class="container">
				<div class="row">
					<div class="col-sm-3" style="font-weight: bold;"><?php echo $res->question_text ?></div>
					<div class="col-sm-9"><?php echo $res->answer_value ?></div>
				</div>
				<div>&nbsp;</div>
			</div>
			<?php
			endforeach;
			?>
		</div>
		<?php include 'footer.php'; ?>
	</div>	
</body>
</html>
