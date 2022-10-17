<?php

/**
 * The SurveyThankYouController class is a Controller that thanks a user for taking the survey.
 *
 * @author David Barnes
 * @copyright Copyright (c) 2013, David Barnes
 */
class SurveyConfirmController extends Controller
{
    /**
     * Handle the page request.
     *
     * @param array $request the page parameters from a form post or query string
     */
    protected function handleRequest(&$request)
    {
        $survey = $this->getSurvey($request);
        $this->assign('survey', $survey);
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
    protected function getSurvey($request)
    {
        $this->assign('event', $request);
        $this->assign('title', "");
    }
}
