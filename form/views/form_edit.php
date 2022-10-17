<!DOCTYPE html>
<html>
<head>
	<title>Form Builder | Form</title>
	<link rel="icon" href="images/rm.png">
	<?php include 'stylesheets.php'; ?>
	<?php include 'scripts.php'; ?>
	<script type="text/javascript">
		var questionHtml = <?php ob_start(); include 'form_edit_question.php'; echo json_encode(ob_get_clean()); ?>;
		var choiceHtml = <?php ob_start(); include 'form_edit_choice.php'; echo json_encode(ob_get_clean()); ?>;
			<?php if (empty($survey->survey_id)): ?>
				$(function() {
					$('#survey_name').focus();
				});
			<?php endif; ?>
	</script>
	<script type="text/javascript" src="js/form_edit.js"></script>
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
			<h3>Form Information</h3>
			<br>

			<?php if (!empty($survey) && $survey instanceof Survey): ?>
				<form id="survey_edit_form" action="form_edit.php" method="post">
				
					<input type="hidden" id="action" name="action" value="edit_survey" />
					<input type="hidden" id="form_id" name="form_id" value="<?= htmlspecialchars($survey->survey_id) ?>" />

					<div class="form-grid-temp-1">
						<label>Form Title:</label>
						<input type="text" id="survey_name" name="survey_name" value="<?= htmlspecialchars($survey->survey_name) ?>" />
					</div>

					<div class="questions_container">
						<h3>Questions</h3>
						<div class="questions">
							<?php foreach ($survey->questions as $i => $question): ?>
							<?php include 'form_edit_question.php'; ?>
							<?php endforeach; ?>
						</div>

						<button class="btn btn-primary" id="add_question">Add Question</button>
					</div>

					<div class="float-right">
						<button class="btn btn-primary" id="delete_survey" name="delete_survey">Delete Form</button>
						<button class="btn btn-primary" id="submitButton" name="submitButton">Save</button>
					</div>

				</form>
			<?php endif; ?>
			
		</div>
		<?php include 'footer.php'; ?>
	</div>

</body>
</html>
