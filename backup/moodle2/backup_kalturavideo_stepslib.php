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
 * Define the complete kalturavideo structure for backup, with file and id annotations
 */
class backup_kalturavideo_activity_structure_step extends backup_activity_structure_step {

    protected function define_structure() {

        //the kalturavideo module stores no user info

        // Define each element separated
        $kalturavideo = new backup_nested_element('kalturavideo', array('id'), array(
            'course','name', 'intro', 'introformat', 'kalturaentry','videotype',
            'display', 'displayoptions', 'parameters', 'timemodified'));


        // Build the tree
        //nothing here for videos

        // Define sources
        $kalturavideo->set_source_table('kalturavideo', array('id' => backup::VAR_ACTIVITYID));

        // Define id annotations
        //module has no id annotations

        // Define file annotations
        $kalturavideo->annotate_files('mod_kalturavideo', 'intro', null); // This file area hasn't itemid

        // Return the root element (kalturavideo), wrapped into standard activity structure
        return $this->prepare_activity_structure($kalturavideo);

    }
}
