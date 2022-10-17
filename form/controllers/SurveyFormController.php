<?php

/**
 * The SurveyFormController class is a Controller that allows a user to take a survey.
 *
 * @author David Barnes
 * @copyright Copyright (c) 2013, David Barnes
 */

require $_SERVER['DOCUMENT_ROOT'].'/vendor/PHPMailer/src/Exception.php';
require $_SERVER['DOCUMENT_ROOT'].'/vendor/PHPMailer/src/PHPMailer.php';
require $_SERVER['DOCUMENT_ROOT'].'/vendor/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class SurveyFormController extends Controller
{
    /**
     * Handle the page request.
     *
     * @param array $request the page parameters from a form post or query string
     */
    protected function handleRequest(&$request)
    {
        if($request['form_title'] == "CoachingTheElinkTechnique")
            $request['form_title'] = "CoachingTheElinkTechniqueVersion2";
        
        $survey = $this->getSurvey($request);
        $this->assign('survey', $survey);

        if (isset($request['action'])) {
            $this->handleAction($request);
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
            case 'add_survey_response':
                $this->addSurveyResponse($request);
                break;
        }
    }

    /**
     * Query the database for a survey_id.
     *
     * @param array $request the page parameters from a form post or query string
     *
     * @return Survey $survey returns a Survey object
     *
     * @throws Exception throws exception if survey id is not specified or not found
     */
    protected function getSurvey(&$request)
    {
        if (! empty($request['form_title'])) {
            $survey = Survey::queryRecordWithWhereClause($this->pdo, 'survey_name = :survey_name', ['survey_name' => $request['form_title']]);
            if (! $survey) {
                throw new Exception('Form Title not found in database');
            }
            $survey->getQuestions($this->pdo);
            foreach ($survey->questions as $question) {
                $question->getChoices($this->pdo);
            }
        } else {
            throw new Exception('Form Title must be specified');
        }

        return $survey;
    }

    /**
     * Set the values for the survey object based on form parameters.
     *
     * @param Survey         $survey         the survey object
     * @param SurveyResponse $surveyResponse the survey response object to update
     * @param array          $request        the page parameters from a form post or query string
     */
    protected function setSurveyResponseValues(Survey $survey, SurveyResponse $surveyResponse, &$request)
    {
        $surveyResponse->survey_id = $survey->survey_id;
        $surveyResponse->time_taken = gmdate('Y-m-d H:i:s');
        $surveyResponse->answers = [];

        if (! empty($request['question_id'])) {
            foreach ($request['question_id'] as $questionID => $answerArray) {
                if (! is_array($answerArray)) {
                    $answerArray = [$answerArray];
                }

                foreach ($answerArray as $answerValue) {
                    $surveyAnswer = new SurveyAnswer;
                    $surveyAnswer->question_id = $questionID;
                    $surveyAnswer->answer_value = $answerValue;
                    $surveyResponse->answers[] = $surveyAnswer;
                }
            }
        }
    }

    /**
     * Store survey answers and survey response record in database.
     *
     * @param SurveyResponse $surveyResponse the survey response object to store
     */
    protected function storeSurveyResponse(SurveyResponse $surveyResponse)
    {
        $surveyResponse->storeRecord($this->pdo);

        foreach ($surveyResponse->answers as $answer) {
            $answer->survey_response_id = $surveyResponse->survey_response_id;
            $answer->storeRecord($this->pdo);
        }
    }

    /**
     * Add a survey response based on POST parameters.
     *
     * @param array $request the page parameters from a form post or query string
     */
    protected function notifyOwners($obj){
        $mail = new PHPMailer();
        $mail->SMTPDebug = 0;  
        $mail->isSMTP();                                            // Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                    // Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
        $mail->Username   = 'sysdev.elink@gmail.com';                     // SMTP username
        $mail->Password   = 'dev116@!';                               // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  
        $mail->Port       = 587;  
        $mail->setFrom('noreply@eLink.com', 'eLink');
        $mail->addAddress($obj['aro_email'], $obj['aro_name']);
        $mail->AddCC("stephaniearcilla@elink.com.ph");
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = 'Content Endorsement: '.$obj['content_type']. ' '.$obj['posting_type']. ' - '.$obj['aro_name'];
    
        $mail->Body    = "Good day ".$obj['aro_name'].",
            <br><br>This is to confirm for a successfull entry to the Content Endorsement Form:
            <br><br>
            <table border='0'>
                <tr>
                    <th>Author's Relations Officer</th>
                    <td>". $obj['aro'] ."</td>
                </tr>
                <tr>
                    <th>Content Type</th>
                    <td>". $obj['content_type'] ."</td>
                </tr>
                <tr>
                    <th>Posting Type</th>
                    <td>". $obj['posting_type'] ."</td>
                </tr>
                <tr>
                    <th>Name of the Show</th>
                    <td>". $obj['show_name'] ."</td>
                </td>
                <tr>
                    <th>Author's Name</th>
                    <td>". $obj['author_name'] ."</td>
                </tr>
                <tr>
                    <th>Book Title</th>
                    <td>". $obj['book_title'] ."</td>
                </tr>
                <tr>
                    <th>Book Description</th>
                    <td>". $obj['book_des'] ."</td>
                </tr>
                <tr>
                    <th>Amazon URL</th>
                    <td>". $obj['amazon_url'] ."</td>
                </tr>
            </table>
            <br><br>Note: The information above are official and shall be reflected in our official catalogue for the show registered. Should you need a correction or change on any piece of information, please contact me so we have it applied.";
        $mail->send();
    }
    
    protected function addSurveyResponse(&$request)
    {
        $this->pdo->beginTransaction();

        // Get survey from database or create a new survey object
        $survey = $this->getSurvey($request);
        
        $surveyResponse = new SurveyResponse;

        // Set values on survey response object
        $this->setSurveyResponseValues($survey, $surveyResponse, $request);

        // Store survey answers and survey response record in database

        $this->storeSurveyResponse($surveyResponse);
        $survey->survey_response_id = $surveyResponse->survey_response_id;
        
        if($survey->survey_id == 19):
            $coach_obj = [
                'survey_id'         => $survey->survey_id,
                'employee'          => $surveyResponse->answers[0]->answer_value." ".$surveyResponse->answers[1]->answer_value,
                'employee_email'    => $surveyResponse->answers[2]->answer_value,
                'coach'             => $surveyResponse->answers[5]->answer_value,
                'coach_email'       => $surveyResponse->answers[6]->answer_value
            ];
            $this->notifyCoach($coach_obj,$survey);
        endif;
        
        if($survey->survey_id == 13):
            $obj = [
                'survey_id'         => $survey->survey_id,
                'aro'               => $surveyResponse->answers[0]->answer_value,
                'content_type'      => $surveyResponse->answers[1]->answer_value,
                'posting_type'      => $surveyResponse->answers[2]->answer_value,
                'show_name'         => $surveyResponse->answers[3]->answer_value,
                'author_name'       => $surveyResponse->answers[4]->answer_value,
                'book_title'        => $surveyResponse->answers[5]->answer_value,
                'book_des'          => $surveyResponse->answers[6]->answer_value,
                'amazon_url'        => $surveyResponse->answers[7]->answer_value,
                'aro_name'          => "",
                'aro_email'         => ""
            ];
            $obj['email'] = "sysdev.elink@gmail.com";
            switch($obj['aro']):
                case "Paul Libaton":
                    $obj['aro_name'] ="Paul";
                    $obj['aro_email'] = "paullibaton@readersmagnet.com";
                break;
                case "Erica Andersen":
                    $obj['aro_name'] = "Erica";
                    $obj['aro_email'] = "ericaandersen@readersmagnet.com";
                break;
                case "Kyle Torrez":
                    $obj['aro_name'] = "Kyle";
                    $obj['aro_email'] = "kyletorrez@readersmagnet.com";
                break;
                case "Jam Comoyong":
                    $obj['aro_name'] ="Jam";
                    $obj['aro_email'] = "jamcomoyong@readersmagnet.com";
                break;
                case "Ryza Rivers":
                    $obj['aro_name'] = "Ryza";
                    $obj['aro_email'] = "ryzarivers@readersmagnet.com";
                break;
                case "Shayne Antonio":
                    $obj['aro_name'] = "Shayne";
                    $obj['aro_email'] = "shayneantonio@readersmagnet.com";
                break;
            endswitch;
            $this->notifyOwners($obj);
        endif;
        
        if($survey->survey_id == 14):
            $obj = [
                'survey_id' => $survey->survey_id,
                'aro'       => $surveyResponse->answers[0]->answer_value,
                'event'     => $surveyResponse->answers[2]->answer_value
            ];
            $obj['email'] = "sysdev.elink@gmail.com";
            switch($obj['aro']):
                case "Paul Libaton":
                    $obj['name'] ="Paul";
                    $obj['email'] = "paullibaton@readersmagnet.com";
                break;
                case "Erica Andersen":
                    $obj['name'] = "Erica";
                    $obj['email'] = "ericaandersen@readersmagnet.com";
                break;
                case "Kyle Torrez":
                    $obj['name'] = "Kyle";
                    $obj['email'] = "kyletorrez@readersmagnet.com";
                break;
                case "Jam Comoyong":
                    $obj['name'] ="Jam";
                    $obj['email'] = "jamcomoyong@readersmagnet.com";
                break;
                case "Ryza Rivers":
                    $obj['name'] = "Ryza";
                    $obj['email'] = "ryzarivers@readersmagnet.com";
                break;
                case "Shayne Antonio":
                    $obj['name'] = "Shayne";
                    $obj['email'] = "shayneantonio@readersmagnet.com";
                break;
            endswitch;
        endif;
        
        if($survey->survey_id == 21):
            $coach_obj = [
                'survey_id'         => $survey->survey_id,
                'employee'          => $surveyResponse->answers[0]->answer_value." ".$surveyResponse->answers[1]->answer_value,
                'employee_email'    => $surveyResponse->answers[2]->answer_value,
                'coach'             => $surveyResponse->answers[5]->answer_value,
                'coach_email'       => $surveyResponse->answers[6]->answer_value
            ];
            $this->notifyCoach($coach_obj,$survey);
        endif;
     
        if($this->pdo->commit()) {
            $this->notifyFormCreator($survey);
            if($survey->survey_id == 14){
                $this->notifyARO($obj,$survey);
                $this->redirect('form_confirm.php?event=' . $obj['event']);
            }
        }

        $this->redirect('form_thank_you.php?form_title=' . $survey->survey_name);
    }

    /*
        Notify through email the form creator
    */
    protected function notifyFormCreator($form) {
        $creator = Login::queryRecordById($this->pdo, $form->created_by);

        $mail = new PHPMailer();
        $mail->SMTPDebug = 0;  
        $mail->isSMTP();                                            // Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                    // Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
        $mail->Username   = 'sysdev.elink@gmail.com';                     // SMTP username
        $mail->Password   = 'dev116@!';                               // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  
        $mail->Port       = 587;  
        $mail->setFrom('noreply@eLink.com', 'eLink');
        $mail->addAddress($creator->email, $creator->first_name);
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = 'New Form Entry - '.$form->survey_name;
        if($form->survey_id == 14){
            $mail->Body    = "Hi ".$creator->first_name.",
            <br><br>There is a new entry on your form <b>".$form->survey_name."</b>.
            <br><br><a href=\"http://".$_SERVER['HTTP_HOST']."/view_results.php?res_id=".$form->survey_response_id."\">View Survey Response</a>
            <br><a href=\"http://".$_SERVER['HTTP_HOST']."/view_all_results.php?form_id=14\">View All Results</a>";
        }else{
            $mail->Body    = "Hi ".$creator->first_name.",
            <br><br>There is a new entry on your form <b>".$form->survey_name."</b>.
            <br><br><a href=\"http://".$_SERVER['HTTP_HOST']."/view_results.php?res_id=".$form->survey_response_id."\">View Now</a>";
        }
        $mail->send();
    }

    protected function notifyARO($obj,$form){
        $mail = new PHPMailer();
        $mail->SMTPDebug = 0;  
        $mail->isSMTP();                                            // Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                    // Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
        $mail->Username   = 'sysdev.elink@gmail.com';                     // SMTP username
        $mail->Password   = 'dev116@!';                               // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  
        $mail->Port       = 587;  
        $mail->setFrom('noreply@eLink.com', 'eLink');
        $mail->addAddress($obj['email'], $obj['name']);
        //$mail->addAddress('rene.abellana@gmail.com', $obj['name']);
        //$mail->AddCC('mktgfulfillment@readersmagnet.com');
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = 'Attention: ReadersMagnet('.$obj['event'] . ') for: '.$obj['name'];
    
            $mail->Body    = "Hello ".$obj['name'].",
            <br><br>A new event for RM Show Registration - <b>".$obj['event']."</b>.
            <br><br><a href=\"http://".$_SERVER['HTTP_HOST']."/view_results.php?res_id=".$form->survey_response_id."\">View Survey Response</a>
            <br><a href=\"http://".$_SERVER['HTTP_HOST']."/view_all_results.php?form_id=14\">View All Results</a>";
        $mail->send();
    }

    protected function notifyCoach($obj,$form){
        $mail = new PHPMailer();
        $mail->SMTPDebug = 0;  
        $mail->isSMTP();                                            // Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                    // Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
        $mail->Username   = 'sysdev.elink@gmail.com';                     // SMTP username
        $mail->Password   = 'dev116@!';                               // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  
        $mail->Port       = 587;  
        $mail->setFrom('noreply@eLink.com', 'eLink');
        $mail->addAddress($obj['coach_email'], $obj['coach']);
        $mail->AddCC($obj['employee_email']);
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = 'Coaching Session with '.$obj['employee'];
    
        $mail->Body    = "Good day ".$obj['coach'].",
            <br><br>This is to confirm a coaching session with ".$obj['employee'].".
            <br><br><a href=\"http://".$_SERVER['HTTP_HOST']."/view_results.php?res_id=".$form->survey_response_id."\">Please click here to view the coaching information and details.</a>";
        $mail->send();
    }
}