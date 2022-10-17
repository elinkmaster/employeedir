<?php

/**
 * The SurveyEditController class is a Controller that allows a user to enter a survey
 * or make changes to an existing survey.
 *
 * @author David Barnes
 * @copyright Copyright (c) 2013, David Barnes
 */
class SurveyEditController extends Controller
{
    /**
     * Handle the page request.
     *
     * @param array $request the page parameters from a form post or query string
     */
    protected function handleRequest(&$request)
    {
        $user = $this->getUserSession();
        $this->assign('user', $user);

        if (isset($request['action'])) {
            $this->handleAction($request);
        }

        $survey = $this->getSurvey($request);
        $this->assign('survey', $survey);

        if (isset($request['status']) && $request['status'] == 'success') {
            $this->assign('statusMessage', 'Form updated successfully');
        }
    }

    /**
     * Handle a user submitted action.
     *
     * @param array $request the page parameters from a form post or query string
     */
    protected function handleAction(&$request)
    {
        switch ($request['action']) {
            case 'get_survey':
                $this->getSurvey($request['form_id']);
                break;

            case 'edit_survey':
                $this->editSurvey($request);
                break;

            case 'delete_survey':
                $this->deleteSurvey($request);
                break;
        }
    }

    /**
     * Query the database for a survey_id or create an empty survey.
     *
     * @param array $request the page parameters from a form post or query string
     *
     * @return Survey $survey returns a Survey object
     *
     * @throws Exception throws exception if survey id is not found
     */
    protected function getSurvey(&$request)
    {
        if (! empty($request['form_id'])) {
            $survey = Survey::queryRecordById($this->pdo, $request['form_id']);
            if (! $survey) {
                throw new Exception('Form ID not found in database');
            }
            // Keep track of existing ids so that any records not updated are deleted
            $survey->existing_question_ids = [];
            $survey->existing_choice_ids = [];

            $survey->getQuestions($this->pdo);
            foreach ($survey->questions as $question) {
                $survey->existing_question_ids[] = $question->question_id;
                $question->getChoices($this->pdo);

                foreach ($question->choices as $choice) {
                    $survey->existing_choice_ids[] = $choice->choice_id;
                }
            }
        } else {
            $survey = new Survey;
            $survey->questions = [];

            // Create 1 empty question
            $question = new Question;
            $question->question_type = 'checkbox';
            $question->choices = [];

            // Create 1 empty choice
            $choice = new Choice;
            $question->choices[] = $choice;

            $survey->questions[] = $question;
        }

        return $survey;
    }

    /**
     * Set the values for the survey object based on form parameters.
     *
     * @param Survey $survey  the survey object to update
     * @param array  $request the page parameters from a form post or query string
     */
    protected function setSurveyValues(Survey $survey, &$request)
    {
        $user = $this->getUserSession();
        
        $survey->updateValues($request);

        if($survey->survey_id == null) {
            $survey->created_by = $user->login_id;
            $survey->created_at = gmdate('Y-m-d H:i:s');
        } else {
            $survey->modified_by = $user->login_id;
            $survey->modified_at = gmdate('Y-m-d H:i:s');
        }

        $this->setSurveyQuestions($survey, $request);
    }

    /**
     * Set the survey's questions based on form parameters.
     *
     * @param Survey $survey  the survey object to update
     * @param array  $request the page parameters from a form post or query string
     */
    protected function setSurveyQuestions(Survey $survey, &$request) {
        if (! empty($request['question_type'])) {
            $survey->questions = [];
            $questionOrder = 1;
            foreach ($request['question_type'] as $questionID => $questionType) {
                $question = new Question;
                if (is_numeric($questionID)) {
                    $question->question_id = $questionID;
                }
                $question->question_type = $questionType;
                $question->question_text = $request['question_text'][$questionID];
                $question->is_required = isset($request['is_required'][$questionID]) ? 1 : 0;
                $question->question_order = $questionOrder++;

                $this->setQuestionChoices($question, $questionID, $request);
                $survey->questions[] = $question;
            }
        }
    }

    /**
     * Set the question's choices based on form parameters.
     *
     * @param Question $question the question object to update
     * @param array    $request  the page parameters from a form post or query string
     */
    protected function setQuestionChoices(Question $question, $questionID, &$request)
    {
        if (in_array($question->question_type, ['select', 'radio', 'checkbox']) && isset($request['choice_text'][$questionID])) {
            $question->choices = [];
            $choiceOrder = 1;
            foreach ($request['choice_text'][$questionID] as $choiceID => $choiceText) {
                $choice = new Choice;
                if (is_numeric($choiceID)) {
                    $choice->choice_id = $choiceID;
                }
                $choice->choice_text = $choiceText;
                $choice->choice_order = $choiceOrder++;
                $question->choices[] = $choice;
            }
        }
    }

    /**
     * Store the survey, questions and choices in the database.
     *
     * @param Survey $survey the survey object to store in the database
     */
    protected function storeSurvey(Survey $survey)
    {
        // Keep track of stored ids so that any records not updated are deleted
        $stored_question_ids = [];
        $stored_choice_ids = [];

        $survey->storeRecord($this->pdo);

        foreach ($survey->questions as $question) {
            $question->survey_id = $survey->survey_id;
            $question->storeRecord($this->pdo);
            $stored_question_ids[] = $question->question_id;

            foreach ($question->choices as $choice) {
                $choice->question_id = $question->question_id;
                $choice->storeRecord($this->pdo);
                $stored_choice_ids[] = $choice->choice_id;
            }
        }

        // Delete choices that were removed
        if (! empty($survey->existing_choice_ids)) {
            $deleted_choice_ids = array_diff($survey->existing_choice_ids, $stored_choice_ids);
            Choice::deleteChoices($this->pdo, $deleted_choice_ids);
        }

        // Delete questions that were removed
        if (! empty($survey->existing_question_ids)) {
            $deleted_question_ids = array_diff($survey->existing_question_ids, $stored_question_ids);
            Question::deleteQuestions($this->pdo, $deleted_question_ids);
        }
    }

    /**
     * Update a survey based on POST parameters.
     *
     * @param array $request the page parameters from a form post or query string
     */
    protected function editSurvey(&$request)
    {
        $this->pdo->beginTransaction();

        // Get survey from database or create a new survey object
        $survey = $this->getSurvey($request);

        // Set values on survey object
        $this->setSurveyValues($survey, $request);

        // Store survey, question and choice records in database
        $this->storeSurvey($survey);

        $this->pdo->commit();

        $this->redirect('form_edit.php?form_id=' . $survey->survey_id . '&status=success');
    }

    /**
     * Delete a survey based on the survey_id specified in the POST parameters.
     *
     * @param array $request the page parameters from a form post or query string
     */
    protected function deleteSurvey(&$request)
    {
        if (! empty($request['form_id'])) {
            $this->pdo->beginTransaction();

            $survey = Survey::queryRecordById($this->pdo, $request['form_id']);
            $survey->deleteRecord($this->pdo);

            $this->pdo->commit();

            $this->redirect('forms.php?status=deleted');
        }
    }

    /**
     * Check if Survey Name is already exist
     *
     */
    public function checkSurveyNameExist() {
        $this->openDatabase();
        $this->pdo->beginTransaction();
        $exist = Survey::checkSurveyNameExist($this->pdo, $_POST['survey_id'], $_POST['survey_name']);
        $this->pdo->commit();
        echo json_encode($exist);
    }

}
