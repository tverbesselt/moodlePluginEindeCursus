<?php
class block_cursusadministratie extends block_base {
    public function init() {
        $this->title = get_string('pluginname', 'block_cursusadministratie');
    }

    public function get_content() {
        global $COURSE, $OUTPUT, $USER, $DB;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass();
        
        // Only show to users who can manage the course (teachers/managers)
        $context = context_course::instance($COURSE->id);
        if (!has_capability('block/cursusadministratie:view', $context)) {
            return $this->content;
        }

        // Calculate progress
        $fs = get_file_storage();
        $record = $DB->get_record('block_cursusadministratie', ['courseid' => $COURSE->id]);
        
        $items = ['exam', 'solution', 'filled_exam', 'moduleplan', 'progressplan'];
        $completed = 0;
        
        foreach ($items as $item) {
            $has_file = false;
            // Check for files
            $files = $fs->get_area_files($context->id, 'block_cursusadministratie', $item, 0, 'sortorder', false);
            if (count($files) > 0) {
                $has_file = true;
            }
            
            // Check for paper submission
            $paper_field = $item . '_paper';
            $has_paper = $record && !empty($record->$paper_field);
            
            if ($has_file || $has_paper) {
                $completed++;
            }
        }

        $total = count($items);
        
        $url = new moodle_url('/blocks/cursusadministratie/view.php', ['courseid' => $COURSE->id]);
        
        $this->content->text = '';
        $this->content->text .= html_writer::tag('p', get_string('progress_summary', 'block_cursusadministratie', ['completed' => $completed, 'total' => $total]));
        $this->content->text .= $OUTPUT->action_link($url, get_string('manage_files', 'block_cursusadministratie'));

        return $this->content;
    }
    
    public function applicable_formats() {
        return array('course-view' => true);
    }
}
