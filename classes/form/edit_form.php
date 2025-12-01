<?php
namespace block_cursusadministratie\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

class edit_form extends \moodleform {
    public function definition() {
        $mform = $this->_form;
        
        $items = [
            'exam' => 'header_exam',
            'solution' => 'header_solution',
            'filled_exam' => 'header_filled_exam',
            'moduleplan' => 'header_moduleplan',
            'progressplan' => 'header_progressplan'
        ];

        foreach ($items as $key => $header_string) {
            $mform->addElement('header', 'header_' . $key, get_string($header_string, 'block_cursusadministratie'));
            
            // File manager
            $mform->addElement('filemanager', $key . '_file', get_string('file'), null, ['subdirs' => 0, 'maxbytes' => 0, 'maxfiles' => 1]);
            
            // Paper submission checkbox
            $mform->addElement('checkbox', $key . '_paper', get_string('paper_submission', 'block_cursusadministratie'));
        }

        $this->add_action_buttons();
    }
}
