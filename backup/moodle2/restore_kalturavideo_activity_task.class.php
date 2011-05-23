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

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/kalturavideo/backup/moodle2/restore_kalturavideo_stepslib.php'); // Because it exists (must)

/**
 * kalturavideo restore task that provides all the settings and steps to perform one
 * complete restore of the activity
 */
class restore_kalturavideo_activity_task extends restore_activity_task {

    /**
     * Define (add) particular settings this activity can have
     */
    protected function define_my_settings() {
        // No particular settings for this activity
    }

    /**
     * Define (add) particular steps this activity can have
     */
    protected function define_my_steps() {
        // kalturavideo only has one structure step
        $this->add_step(new restore_kalturavideo_activity_structure_step('kalturavideo_structure', 'kalturavideo.xml'));
    }

    /**
     * Define the contents in the activity that must be
     * processed by the link decoder
     */
    static public function define_decode_contents() {
        $contents = array();

        $contents[] = new restore_decode_content('kalturavideo', array('intro'), 'kalturavideo');

        return $contents;
    }

    /**
     * Define the decoding rules for links belonging
     * to the activity to be executed by the link decoder
     */
    static public function define_decode_rules() {
        $rules = array();

        $rules[] = new restore_decode_rule('KALTURAVIDEOINDEX', '/mod/kalturavideo/index.php?id=$1', 'course');
        $rules[] = new restore_decode_rule('KALTURAVIDEOVIEWBYID', '/mod/kalturavideo/view.php?id=$1', 'course_module');
        $rules[] = new restore_decode_rule('KALTURAVIDEOVIEWBYU', '/mod/kalturavideo/view.php?u=$1', 'kalturavideo');

        return $rules;

    }

    /**
     * Define the restore log rules that will be applied
     * by the {@link restore_logs_processor} when restoring
     * kalturavideo logs. It must return one array
     * of {@link restore_log_rule} objects
     */
    static public function define_restore_log_rules() {
        $rules = array();

        $rules[] = new restore_log_rule('kalturavideo', 'add', 'view.php?id={course_module}', '{kalturavideo}');
        $rules[] = new restore_log_rule('kalturavideo', 'update', 'view.php?id={course_module}', '{kalturavideo}');
        $rules[] = new restore_log_rule('kalturavideo', 'view', 'view.php?id={course_module}', '{kalturavideo}');

        return $rules;
    }

    /**
     * Define the restore log rules that will be applied
     * by the {@link restore_logs_processor} when restoring
     * course logs. It must return one array
     * of {@link restore_log_rule} objects
     *
     * Note this rules are applied when restoring course logs
     * by the restore final task, but are defined here at
     * activity level. All them are rules not linked to any module instance (cmid = 0)
     */
    static public function define_restore_log_rules_for_course() {
        $rules = array();

        $rules[] = new restore_log_rule('kalturavideo', 'view all', 'index.php?id={course}', null);

        return $rules;
    }
}
