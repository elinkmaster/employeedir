<?php

	$question_id = 'QUESTION_ID';
	$choice_id = 'CHOICE_ID';
	$choice_text = null;

	if(!empty($question)) {
		$question_id = $question->getUniqueId();
	}

	if(!empty($choice)) {
		$choice_id = $choice->getUniqueId();
		$choice_text = htmlspecialchars($choice->choice_text);
	}

	if(isset($j)) {
		$j += 1;
	}

?>

<div class="choice" data-choice_id="<?= $choice_id ?>" data-choice_number="<?= $j ?>">
	<label>Choice <span class="choice_number"><?= $j ?></span>:</label>
	<input type="text" class="choice_text" name="choice_text[<?= $question_id ?>][<?= $choice_id ?>]" value="<?= $choice_text ?>" />
	<button class="delete_choice">Delete Choice</button>
</div>