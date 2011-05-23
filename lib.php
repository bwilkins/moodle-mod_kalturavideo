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

defined('MOODLE_INTERNAL') || die;

/**
 * List of features supported in kalturavideo module
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, false if not, null if doesn't know
 */
function kalturavideo_supports($feature) {
    switch($feature) {
        case FEATURE_MOD_ARCHETYPE:           return MOD_ARCHETYPE_RESOURCE;
        case FEATURE_GROUPS:                  return false;
        case FEATURE_GROUPINGS:               return false;
        case FEATURE_GROUPMEMBERSONLY:        return true;
        case FEATURE_MOD_INTRO:               return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS: return true;
        case FEATURE_GRADE_HAS_GRADE:         return false;
        case FEATURE_GRADE_OUTCOMES:          return false;
        case FEATURE_BACKUP_MOODLE2:          return true;

        default: return null;
    }
}

/**
 * Returns all other caps used in module
 * @return array
 */
function kalturavideo_get_extra_capabilities() {
    return array('moodle/site:accessallgroups');
}

/**
 * This function is used by the reset_course_userdata function in moodlelib.
 * @param $data the data submitted from the reset course.
 * @return array status array
 */
function kalturavideo_reset_userdata($data) {
    return array();
}

/**
 * List of view style log actions
 * @return array
 */
function kalturavideo_get_view_actions() {
    return array('view', 'view all');
}

/**
 * List of update style log actions
 * @return array
 */
function kalturavideo_get_post_actions() {
    return array('update', 'add');
}

/**
 * Add kalturavideo instance.
 * @param object $data
 * @param object $mform
 * @return int new kalturavideo instance id
 */
function kalturavideo_add_instance($data, $mform) {
    global $DB;



    $data->timemodified = time();
    $displayoptions=array();
    $displayoptions['printheading'] = (int)!empty($data->printheading);
    $displayoptions['printintro'] = (int)!empty($data->printintro);
    $data->displayoptions = serialize($displayoptions);
    $data->id = $DB->insert_record('kalturavideo', $data);

    return $data->id;
}

/**
 * Update kalturavideo instance.
 * @param object $data
 * @param object $mform
 * @return bool true
 */
function kalturavideo_update_instance($data, $mform) {
    global $CFG, $DB;



    $data->timemodified = time();
    $displayoptions=array();
    $displayoptions['printheading'] = (int)!empty($data->printheading);
    $displayoptions['printintro'] = (int)!empty($data->printintro);
    $data->displayoptions = serialize($displayoptions);
    $data->id           = $data->instance;

    $DB->update_record('kalturavideo', $data);

    return true;
}

/**
 * Delete kalturavideo instance.
 * @param int $id
 * @return bool true
 */
function kalturavideo_delete_instance($id) {
    global $DB;

    if (!$kalturavideo = $DB->get_record('kalturavideo', array('id'=>$id))) {
        return false;
    }

    // note: all context files are deleted automatically

    $DB->delete_records('kalturavideo', array('id'=>$kalturavideo->id));

    return true;
}

/**
 * Return use outline
 * @param object $course
 * @param object $user
 * @param object $mod
 * @param object $kalturavideo
 * @return object|null
 */
function kalturavideo_user_outline($course, $user, $mod, $kalturavideo) {
    global $DB;

    if ($logs = $DB->get_records('log', array('userid'=>$user->id, 'module'=>'kalturavideo',
                                              'action'=>'view', 'info'=>$kalturavideo->id), 'time ASC')) {

        $numviews = count($logs);
        $lastlog = array_pop($logs);

        $result = new stdClass();
        $result->info = get_string('numviews', '', $numviews);
        $result->time = $lastlog->time;

        return $result;
    }
    return NULL;
}

/**
 * Return use complete
 * @param object $course
 * @param object $user
 * @param object $mod
 * @param object $kalturavideo
 */
function kalturavideo_user_complete($course, $user, $mod, $kalturavideo) {
    global $CFG, $DB;

    if ($logs = $DB->get_records('log', array('userid'=>$user->id, 'module'=>'kalturavideo',
                                              'action'=>'view', 'info'=>$kalturavideo->id), 'time ASC')) {
        $numviews = count($logs);
        $lastlog = array_pop($logs);

        $strmostrecently = get_string('mostrecently');
        $strnumviews = get_string('numviews', '', $numviews);

        echo "$strnumviews - $strmostrecently ".userdate($lastlog->time);

    } else {
        print_string('neverseen', 'kalturavideo');
    }
}

/**
 * Returns the users with data in one kalturavideo
 *
 * @param int $kalturavideoid
 * @return bool false
 */
function kalturavideo_get_participants($kalturavideoid) {
    return false;
}

/**
 * Given a course_module object, this function returns any
 * "extra" information that may be needed when printing
 * this activity in a course listing.
 *
 * See {@link get_array_of_activities()} in course/lib.php
 *
 * @param object $coursemodule
 * @return object info
 */
function kalturavideo_get_coursemodule_info($coursemodule) {
    global $CFG, $DB;
    require_once("$CFG->dirroot/local/kaltura/lib.php");

    if (!$kalturavideo = $DB->get_record('kalturavideo', array('id'=>$coursemodule->instance), 'id, name, display, displayoptions, kalturavideo, parameters')) {
        return NULL;
    }

    $info = new stdClass();
    $info->name = $kalturavideo->name;

    //note: there should be a way to differentiate links from normal resources
    $info->icon = kalturavideo_guess_icon($kalturavideo->kalturavideo);

    $display = kalturavideo_get_final_display_type($kalturavideo);

    if ($display == RESOURCELIB_DISPLAY_POPUP) {
        $fullkalturavideo = "$CFG->wwwroot/mod/kalturavideo/view.php?id=$coursemodule->id&amp;redirect=1";
        $options = empty($kalturavideo->displayoptions) ? array() : unserialize($kalturavideo->displayoptions);
        $width  = empty($options['popupwidth'])  ? 620 : $options['popupwidth'];
        $height = empty($options['popupheight']) ? 450 : $options['popupheight'];
        $wh = "width=$width,height=$height,toolbar=no,location=no,menubar=no,copyhistory=no,status=no,directories=no,scrollbars=yes,resizable=yes";
        $info->extra = "onclick=\"window.open('$fullkalturavideo', '', '$wh'); return false;\"";

    } else if ($display == RESOURCELIB_DISPLAY_NEW) {
        $fullkalturavideo = "$CFG->wwwroot/mod/kalturavideo/view.php?id=$coursemodule->id&amp;redirect=1";
        $info->extra = "onclick=\"window.open('$fullkalturavideo'); return false;\"";

    } else if ($display == RESOURCELIB_DISPLAY_OPEN) {
        $fullkalturavideo = "$CFG->wwwroot/mod/kalturavideo/view.php?id=$coursemodule->id&amp;redirect=1";
        $info->extra = "onclick=\"window.location.href ='$fullkalturavideo';return false;\"";
    }

    return $info;
}

/**
 * This function extends the global navigation for the site.
 * It is important to note that you should not rely on PAGE objects within this
 * body of code as there is no guarantee that during an AJAX request they are
 * available
 *
 * @param navigation_node $navigation The kalturavideo node within the global navigation
 * @param stdClass $course The course object returned from the DB
 * @param stdClass $module The module object returned from the DB
 * @param stdClass $cm The course module instance returned from the DB
 */
function kalturavideo_extend_navigation($navigation, $course, $module, $cm) {
    /**
     * This is currently just a stub so that it can be easily expanded upon.
     * When expanding just remove this comment and the line below and then add
     * you content.
     */
    $navigation->nodetype = navigation_node::NODETYPE_LEAF;
}

function kalturavideo_guess_icon() {
    return false;
}

function kalturavideo_get_final_display_type(){}
