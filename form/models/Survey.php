<?php

/**
 * The Survey class is a Model representing the survey table, used to get questions
 * associated with the survey and to get survey responses and survey counts for reporting.
 *
 * @author David Barnes
 * @copyright Copyright (c) 2013, David Barnes
 */
class Survey extends Model
{
    public $questions = [];
    public $responses = [];
    // The primary key used to uniquely identify a record
    protected static $primaryKey = 'survey_id';

    // The list of fields in the table
    protected static $fields = [
        'survey_id',
        'survey_name',
        'created_by',
        'created_at',
        'modified_by',
        'modified_at'
    ];

    /**
     * Get the question records associated with this survey, and assign to
     * $questions instance variable.
     *
     * @param PDO $pdo the database to search in
     */
    public function getQuestions(PDO $pdo)
    {
        $search = ['survey_id' => $this->survey_id, 'sort' => 'question_order'];
        $this->questions = Question::queryRecords($pdo, $search);
    }

    /**
     * Get all survey responses for this survey and assign it to the
     * $responses instance variable.
     *
     * @param PDO $pdo the database to search in
     */
    public function getSurveyResponses(PDO $pdo)
    {
        $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
        $groupConcatSql = 'group_concat';
        if ($driver == 'pgsql') {
            $groupConcatSql = 'string_agg';
        }

        if (! empty($this->survey_id)) {
            if (empty($this->questions)) {
                $this->getQuestions($pdo);
            }

            $questionSubSelects = [];
            foreach ($this->questions as $question) {
                $questionSubSelects[] = "(select $groupConcatSql(answer_value) from survey_answer sa
                                          where sa.survey_response_id = sr.survey_response_id and
                                          sa.question_id = :question_id_{$question->question_id}) as question_{$question->question_id}";
                $params["question_id_{$question->question_id}"] = $question->question_id;
            }
            $questionSubSelectSql = implode(', ', $questionSubSelects);
            $sql = "select sr.*, $questionSubSelectSql from survey_response sr where sr.survey_id = :survey_id and sr.status = 1";
            $params['survey_id'] = $this->survey_id;

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $stmt->setFetchMode(PDO::FETCH_OBJ);
            $this->responses = $stmt->fetchAll();
        }
    }

    /**
     * Get the survey response counts for every non-open text question for this survey
     * and assign it to the $choice_counts instance variable of each question object.
     *
     * @param PDO $pdo the database to search in
     */
    public function getSurveyResponseCounts(PDO $pdo)
    {
        foreach ($this->questions as $i => $question) {
            if (! in_array($question->question_type, ['select', 'radio', 'checkbox'])) {
                unset($this->questions[$i]);
            }
        }

        foreach ($this->questions as $i => $question) {
            $sql = 'select count(*) from survey_answer sa
                    left outer join survey_response sr on sr.survey_response_id = sa.survey_response_id
                    where sr.survey_id = :survey_id
                    and sa.question_id = :question_id
                    and sr.status = 1
                    and sa.answer_value = :answer_value';
            $stmt = $pdo->prepare($sql);

            $question->max_answer_count = 0;

            foreach ($question->choices as $choice) {
                $params = [
                    'survey_id' => $this->survey_id,
                    'question_id' => $question->question_id,
                    'answer_value' => $choice->choice_text,
                ];
                $stmt->execute($params);
                $stmt->setFetchMode(PDO::FETCH_NUM);
                if ($row = $stmt->fetch()) {
                    $choice->answer_count = $row[0];
                    if ($choice->answer_count > $question->max_answer_count) {
                        $question->max_answer_count = $choice->answer_count;
                    }
                }
            }

            $question->choice_counts = [];
            foreach ($question->choices as $choice) {
                $question->choice_counts[] = [$choice->choice_text, $choice->answer_count];
            }
        }
    }

    /**
     * Get a unique id for this object, using the primary key if the record
     * has been stored in the database, otherwise a generated unique id.
     *
     * @return string|int returns a unique id
     */
    public function getUniqueId()
    {
        if (! empty($this->survey_id)) {
            return $this->survey_id;
        } else {
            static $uniqueID;
            if (empty($uniqueID)) {
                $uniqueID = __CLASS__ . uniqid();
            }

            return $uniqueID;
        }
    }

    /**
     * Check is Survey Name is already exist
     *
     * @return boolean
     */
    public function checkSurveyNameExist(PDO $pdo, $survey_id, $survey_name)
    {
        $count = 0;
        $survey_name = trim(preg_replace('/\s+/', ' ', $survey_name));
        $sql = 'select count(*) from survey where survey_name = :survey_name';
        $params = ['survey_name' => $survey_name];

        if(isset($survey_id)) {
            $sql = 'select count(*) from survey where survey_name = :survey_name and survey_id <> :survey_id';
            $params = ['survey_name' => $survey_name, 'survey_id' => $survey_id];
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $stmt->setFetchMode(PDO::FETCH_NUM);
        if($row = $stmt->fetch()) {
            $count = $row[0];
        }

        return ($count > 0 ? true : false);
    }

    /*
        Get Surveys with full record including join
    */
    public function getSurveysFull(PDO $pdo,$active_user) {
        $params = [];
        if($active_user == 1 || $active_user == 8)
            $sql = 'SELECT *, CONCAT(login.first_name, " ", login.last_name) AS created_by_name FROM survey LEFT JOIN login ON survey.created_by = login.login_id ORDER BY survey_name ASC';
        else if($active_user == 11)
            $sql = 'SELECT *, CONCAT(login.first_name, " ", login.last_name) AS created_by_name FROM survey LEFT JOIN login ON survey.created_by = login.login_id where survey_id = 19 or survey_id = 21 ORDER BY survey_name ASC';
        else if($active_user == 13)
            $sql = 'SELECT *, CONCAT(login.first_name, " ", login.last_name) AS created_by_name FROM survey LEFT JOIN login ON survey.created_by = login.login_id where survey_id <> 19 and survey_id <> 21 ORDER BY survey_name ASC';
        else
            $sql = 'SELECT *, CONCAT(login.first_name, " ", login.last_name) AS created_by_name FROM survey LEFT JOIN login ON survey.created_by = login.login_id where survey.created_by = '.$active_user.' ORDER BY survey_name ASC';
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        return $stmt->fetchAll();
    }

    public function getSurveyRes($survey_response,PDO $pdo){
        $params = [];
        $sql = "SELECT 
                    s.survey_name, q.question_text, sa.answer_value
                FROM
                    formbuilder.survey AS s
                        LEFT JOIN
                    question AS q ON q.survey_id = s.survey_id
                        LEFT JOIN
                    survey_response AS sr ON sr.survey_id = s.survey_id
                        LEFT JOIN
                    survey_answer AS sa ON sa.survey_response_id = sr.survey_response_id
                WHERE
                    sr.survey_response_id = $survey_response
                        AND sa.question_id = q.question_id;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        return $stmt->fetchAll();
    }

    public function markSurvey($id, PDO $pdo){
        $sql = "UPDATE 
                    survey_response as sr
                SET
                    sr.status = 0
                WHERE
                    sr.survey_response_id = $id
                LIMIT 1;";

        $stmt = $pdo->prepare($sql);
        return $stmt->execute();
    }

}
