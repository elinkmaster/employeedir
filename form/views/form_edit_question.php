<?php

	$question_id = 'QUESTION_ID';
	$question_type = null;
	$question_text = null;
	$is_required = 0;

	if(!empty($question)) {
		$question_id = $question->getUniqueId();
		$question_text = htmlspecialchars($question->question_text);
		$question_type = $question->question_type;
		$is_required = $question->is_required;
	}

?>

<div class="question" data-question_id="<?= $question_id ?>" data-question_number="<?= isset($i) ? ($i+1) : '' ?>">

	<div class="question-header">
		<div class="question-btn-tools">
			<button class="move_question_up">Move Up</button>
			<button class="move_question_down">Move Down</button>
			<button class="delete_question">Delete Question</button>
		</div>
		<h4>Question <span class="question_number"><?= isset($i) ? ($i+1) : '' ?></span></h4>
	</div>

	<div class="question-body">

		<div class="form-grid-temp-2">
			<label>Question Type:</label>
			<select class="question_type" name="question_type[<?= $question_id ?>]">
				<option value="text" <?= $question_type == 'text' ? 'selected="selected"' : '' ?> >Short Answer</option>
				<option value="textarea" <?= $question_type == 'textarea' ? 'selected="selected"' : '' ?> >Paragraph</option>
				<option value="radio" <?= $question_type == 'radio' ? 'selected="selected"' : '' ?> >Multiple Choice</option>
				<option value="checkbox" <?= $question_type == 'checkbox' ? 'selected="selected"' : '' ?> >Checkboxes</option>
				<option value="select" <?= $question_type == 'select' ? 'selected="selected"' : '' ?> >Dropdown</option>
				<option value="date" <?= $question_type == 'date' ? 'selected="selected"' : '' ?> >Date</option>
				<option value="time" <?= $question_type == 'time' ? 'selected="selected"' : '' ?> >Time</option>
			</select>
			<div class="question-required">
				<input type="checkbox" id="is_required_<?= $question_id ?>" name="is_required[<?= $question_id ?>]" value="1" <?= $is_required == 1 ? 'checked="checked"' : '' ?> />
				<label for="is_required_<?= $question_id ?>">Required Question</label>
			</div>
		</div>

		<div class="form-grid-temp-1">
			<label>Question Text:</label>
			<input type="text" class="question_text" name="question_text[<?= $question_id ?>]" value="<?= $question_text ?>" />
		</div>

		<div class="choices_container"<?= !in_array($question_type, ['select', 'radio', 'checkbox']) ? 'style="display: none;"' : '' ?> >
			<h5>Choices</h5>
			<div class="choices" data-question_id="<?= $question_id ?>">
				<?php if(!empty($question->choices)): ?>
					<?php foreach ($question->choices as $j => $choice): ?>
					<?php include 'form_edit_choice.php'; ?>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
			<button class="add_choice">Add Choice</button>
		</div>

	</div>

</div>
