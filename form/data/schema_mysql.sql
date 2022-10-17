CREATE TABLE login ( login_id INTEGER NOT NULL PRIMARY KEY
    AUTO_INCREMENT, email TEXT NOT NULL, password TEXT NOT NULL,
    first_name TEXT NOT NULL, last_name TEXT NOT NULL );

CREATE TABLE survey (
    survey_id INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
    survey_name TEXT NOT NULL
);

CREATE TABLE question (
    question_id INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
    survey_id INTEGER NOT NULL,
    question_type TEXT,
    question_text TEXT,
    is_required INTEGER,
    question_order INTEGER,
    FOREIGN KEY (survey_id) REFERENCES survey (survey_id) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE choice (
    choice_id INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
    question_id INTEGER NOT NULL,
    choice_text TEXT,
    choice_order INTEGER,
    FOREIGN KEY (question_id) REFERENCES question (question_id) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE survey_response (
    survey_response_id INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
    survey_id INTEGER NOT NULL,
    time_taken TEXT,
    FOREIGN KEY (survey_id) REFERENCES survey (survey_id) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE survey_answer (
    survey_answer_id INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
    survey_response_id INTEGER NOT NULL,
    question_id INTEGER NOT NULL,
    answer_value TEXT NOT NULL,
    FOREIGN KEY (survey_response_id) REFERENCES survey_response (survey_response_id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES question (question_id) ON UPDATE CASCADE ON DELETE CASCADE
);
