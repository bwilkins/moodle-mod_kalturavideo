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

require_once ($CFG->dirroot.'/course/moodleform_mod.php');
require_once($CFG->dirroot.'/local/kaltura/lib.php');
require_once($CFG->libdir.'/resourcelib.php');
require_once($CFG->dirroot.'/local/kaltura/client/KalturaClient.php');

class mod_kalturavideo_mod_form extends moodleform_mod {
    function definition() {
        global $CFG, $DB, $PAGE;
        $PAGE->requires->js('/local/kaltura/js/kaltura-edit.js');
        $PAGE->requires->js('/local/kaltura/js/kaltura-play.js');
        $PAGE->requires->css('/local/kaltura/styles.css');

        $mform = $this->_form;

        $config = get_config('kalturavideo');

        //-------------------------------------------------------
        $mform->addElement('header', 'general', get_string('general', 'form'));
        $mform->addElement('text', 'name', get_string('name'), array('size'=>'48'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $this->add_intro_editor($config->requiremodintro);

        //-------------------------------------------------------
        $mform->addElement('header', 'content', get_string('contentheader', 'kalturavideo'));
        $mform->addElement('hidden', 'kalturavideo','');

        $ids = explode(',', get_config('local_kaltura', 'player_selections'));
        list($sql, $params) = $DB->get_in_or_equal($ids);
        $records = $DB->get_records_select('kalturaplayers' ,'id '.$sql, $params, '', 'id, name');

        $playeroptions = array();
        foreach ($records as $record) {
            $playeroptions[$record->id] = $record->name;
        }

        $mform->addElement('select', 'kaltura_player', get_string('kalturaplayer', 'kalturavideo'), $playeroptions);

        $mform->addElement('html','<div class="kalturaPlayerEdit"></div>');

        $mform->addElement('submit', 'replacevideo', get_string('replacevideo', 'kalturavideo'));

        $kalturaConfig = array();
        $kalturaConfig['cmid'] = optional_param('update',0,PARAM_INT);
        $kalturaConfig['enable_shared'] = 1;

        $updateJS = kalturaGlobals_js($kalturaConfig);
        $mform->addElement('html',$updateJS);

        //-------------------------------------------------------
        $mform->addElement('header', 'optionssection', get_string('optionsheader', 'kalturavideo'));

        $mform->addElement('checkbox', 'printheading', get_string('printheading', 'kalturavideo'));
        $mform->setDefault('printheading', $config->printheading);
        $mform->setAdvanced('printheading', $config->printheading_adv);

        $mform->addElement('checkbox', 'printintro', get_string('printintro', 'kalturavideo'));
        $mform->setDefault('printintro', $config->printintro);
        $mform->setAdvanced('printintro', $config->printintro_adv);

        $this->standard_coursemodule_elements();

        //-------------------------------------------------------
        $this->add_action_buttons();
    }

    function data_preprocessing(&$default_values) {
        if (!empty($default_values['displayoptions'])) {
            $displayoptions = unserialize($default_values['displayoptions']);
            if (isset($displayoptions['printintro'])) {
                $default_values['printintro'] = $displayoptions['printintro'];
            }
            if (isset($displayoptions['printheading'])) {
                $default_values['printheading'] = $displayoptions['printheading'];
            }
        }
    }

}
