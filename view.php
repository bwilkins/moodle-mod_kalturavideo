<?php
/**
 * Kaltura Video Resource for Moodle 2
 * Copyright (C) 2009 Petr Skoda  (http://skodak.org)
 * Copyright (C) 2011 Catalyst IT (http://www.catalyst.net.nz)
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package    mod
 * @subpackage kalturavideo
 * @author     Brett Wilkins <brett@catalyst.net.nz>
 * @license    http://www.gnu.org/licenses/agpl.html GNU Affero GPL v3 or later
 */

require('../../config.php');
require_once($CFG->dirroot."/local/kaltura/lib.php");
require_once($CFG->libdir . '/completionlib.php');

$id       = optional_param('id', 0, PARAM_INT);        // Course module ID

$cm = get_coursemodule_from_id('kalturavideo', $id, 0, false, MUST_EXIST);
$entry = $DB->get_record('kalturavideo', array('id'=>$cm->instance), '*', MUST_EXIST);

$course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);

require_course_login($course, true, $cm);
$context = get_context_instance(CONTEXT_MODULE, $cm->id);
require_capability('mod/kalturavideo:view', $context);

$config = unserialize($entry->displayoptions);

add_to_log($course->id, 'kalturavideo', 'view', 'view.php?id='.$cm->id, $entry->id, $cm->id);

// Update 'viewed' state if required by completion system
$completion = new completion_info($course);
$completion->set_module_viewed($cm);

$PAGE->set_url('/mod/kalturavideo/view.php', array('id' => $cm->id));
$PAGE->set_title(get_string('modulename','kalturavideo').': '.$entry->name);
$PAGE->requires->js('/local/kaltura/js/kaltura-play.js');

echo $OUTPUT->header();

if ($config['printheading']) {
    echo $OUTPUT->box_start('mod_introbox centerpara', 'kalturavideointro');
    echo '<h2>'.$entry->name.'</h2>';
    echo $OUTPUT->box_end();
}
//print content
echo $OUTPUT->box_start();
echo '<div class="kalturaPlayer" style="margin:auto;"></div>';
echo '<script>window.kaltura = {}; window.kaltura.cmid='.$id.';</script>';
echo $OUTPUT->box_end();

if ($config['printintro']) {
    echo $OUTPUT->box_start();
    echo $entry->intro;
    echo $OUTPUT->box_end();
}

echo $OUTPUT->footer();
?>
