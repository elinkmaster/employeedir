<!DOCTYPE html>
<html>
<head>
	<title>Form - <?= htmlspecialchars($survey->survey_name); ?></title>
	<link rel="icon" href="images/rm.png">
	<?php include 'stylesheets.php'; ?>
	<?php include 'scripts.php'; ?>
	<script type="text/javascript" src="js/form.js"></script>
</head>
<body>
	<?php $title = htmlspecialchars($survey->survey_name); ?>
	<?php include 'public_header.php'; ?>

	<div class="panel">
		<div class="panel-header">
			<div class="panel-title-public">FORM RESPONSE</div>
			<?php if (isset($statusMessage)): ?>
				<p class="bg-success"><?= htmlspecialchars($statusMessage); ?></p>
			<?php endif; ?>
		</div>
		<div class="panel-body">

			<?php if (! empty($survey) && $survey instanceof Survey): ?>

				<form id="survey_form" action="form_response.php" method="post">

					<input type="hidden" id="action" name="action" value="add_survey_response" />
					<input type="hidden" id="form_title" name="form_title" value="<?= htmlspecialchars($survey->survey_name); ?>" />
					
					<?php foreach ($survey->questions as $i => $question): ?>
						<div class="response-question">
							<h4 class="question_text" data-question_id="<?= htmlspecialchars($question->question_id); ?>" data-question_type="<?= htmlspecialchars($question->question_type); ?>" data-is_required="<?= htmlspecialchars($question->is_required); ?>"><?= htmlspecialchars($question->question_text); ?></h4>
							<?php if (in_array($question->question_type, ['radio', 'checkbox'])): ?>
							<?php foreach ($question->choices as $j => $choice): ?>
								<div class="response-choices">
									<?php $question_html_id = 'choice_' . htmlspecialchars($question->question_id) . '_' . htmlspecialchars($choice->choice_id); ?>
									<input id="<?= $question_html_id; ?>" type="<?= htmlspecialchars($question->question_type); ?>" name="question_id[<?= htmlspecialchars($question->question_id); ?>][]" value="<?= htmlspecialchars($choice->choice_text); ?>" />
									<label for="<?= $question_html_id; ?>"><?= htmlspecialchars($choice->choice_text); ?></label>
								</div>
							<?php endforeach; ?>
							<?php elseif ($question->question_type == 'select'): ?>
								<select name="question_id[<?= htmlspecialchars($question->question_id); ?>]">
										<option value="">Choose</option>
									<?php foreach ($question->choices as $j => $choice): ?>
										<option value="<?= htmlspecialchars($choice->choice_text); ?>"><?= htmlspecialchars($choice->choice_text); ?></option>
									<?php endforeach; ?>
								</select>
							<?php elseif ($question->question_type == 'text'): ?>
								<input type="text" name="question_id[<?= htmlspecialchars($question->question_id); ?>]" value="" />
							<?php elseif ($question->question_type == 'textarea'): ?>
								<textarea name="question_id[<?= htmlspecialchars($question->question_id); ?>]" rows="15"></textarea>
							<?php elseif ($question->question_type == 'date'): ?>
								<?php
								if($question->question_id == 108 || $question->question_id ==171):
								?>
									<input type="text" name="question_id[<?= htmlspecialchars($question->question_id); ?>]" value="<?php echo date("m/d/Y") ?>" readonly />
								<?php
								else:
								?>
									<input type="date" name="question_id[<?= htmlspecialchars($question->question_id); ?>]" value="" />
								<?php
								endif;
								?>
							<?php elseif ($question->question_type == 'time'): ?>
								<input type="time" name="question_id[<?= htmlspecialchars($question->question_id); ?>]" value="" />
							<?php endif; ?>
						</div>
					<?php endforeach; ?>

					<br><br>
					<button id="submitButton" name="submitButton">Submit</button>

				</form>

			<?php endif; ?>

			
		</div>
		<?php include 'footer.php'; ?>
	</div>

</body>
</html>
