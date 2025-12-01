<?php
defined('MOODLE_INTERNAL') || die();

/**
 * Get the submission status for a course.
 *
 * @param int $courseid
 * @return stdClass|false Record from block_cursusadministratie or false if not found
 */
function block_cursusadministratie_get_status($courseid) {
    global $DB;
    return $DB->get_record('block_cursusadministratie', ['courseid' => $courseid]);
}

/**
 * Update or insert the submission status for a course.
 *
 * @param stdClass $data Data from the form
 * @param int $courseid
 * @return void
 */
function block_cursusadministratie_save_status($data, $courseid) {
    global $DB;

    $record = $DB->get_record('block_cursusadministratie', ['courseid' => $courseid]);
    
    $newrecord = new stdClass();
    $newrecord->courseid = $courseid;
    $newrecord->exam_paper = !empty($data->exam_paper) ? 1 : 0;
    $newrecord->solution_paper = !empty($data->solution_paper) ? 1 : 0;
    $newrecord->filled_exam_paper = !empty($data->filled_exam_paper) ? 1 : 0;
    $newrecord->moduleplan_paper = !empty($data->moduleplan_paper) ? 1 : 0;
    $newrecord->progressplan_paper = !empty($data->progressplan_paper) ? 1 : 0;
    $newrecord->timemodified = time();

    if ($record) {
        $newrecord->id = $record->id;
        $DB->update_record('block_cursusadministratie', $newrecord);
    } else {
        $DB->insert_record('block_cursusadministratie', $newrecord);
    }
}
