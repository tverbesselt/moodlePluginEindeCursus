<?php
require_once('../../config.php');
require_once('lib.php');
require_once('classes/form/edit_form.php');

$courseid = required_param('courseid', PARAM_INT);

if (!$course = $DB->get_record('course', ['id' => $courseid])) {
    print_error('invalidcourseid');
}

require_login($course);
$context = context_course::instance($course->id);
require_capability('block/cursusadministratie:view', $context);

$PAGE->set_url('/blocks/cursusadministratie/view.php', ['courseid' => $courseid]);
$PAGE->set_context($context);
$PAGE->set_heading($course->fullname);
$PAGE->set_title(get_string('pluginname', 'block_cursusadministratie'));

$mform = new \block_cursusadministratie\form\edit_form(null, ['courseid' => $courseid]);

// Load existing data
$data = new stdClass();
$record = block_cursusadministratie_get_status($courseid);

if ($record) {
    $data->exam_paper = $record->exam_paper;
    $data->solution_paper = $record->solution_paper;
    $data->filled_exam_paper = $record->filled_exam_paper;
    $data->moduleplan_paper = $record->moduleplan_paper;
    $data->progressplan_paper = $record->progressplan_paper;
}

// Prepare file areas
$items = ['exam', 'solution', 'filled_exam', 'moduleplan', 'progressplan'];
$fs = get_file_storage();

foreach ($items as $item) {
    $draftitemid = file_get_submitted_draft_itemid($item . '_file');
    file_prepare_draft_area($draftitemid, $context->id, 'block_cursusadministratie', $item, 0);
    $data->{$item . '_file'} = $draftitemid;
}

$mform->set_data($data);

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/course/view.php', ['id' => $courseid]));
} else if ($fromform = $mform->get_data()) {
    // Save paper status
    block_cursusadministratie_save_status($fromform, $courseid);
    
    // Save files
    foreach ($items as $item) {
        file_save_draft_area_files($fromform->{$item . '_file'}, $context->id, 'block_cursusadministratie', $item, 0);
    }
    
    redirect(new moodle_url('/course/view.php', ['id' => $courseid]), get_string('changessaved'), null, \core\output\notification::NOTIFY_SUCCESS);
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('manage_files', 'block_cursusadministratie'));

$mform->display();

echo $OUTPUT->footer();
