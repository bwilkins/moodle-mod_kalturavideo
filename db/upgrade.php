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
require_once($CFG->dirroot . '/local/kaltura/ajax.php');

function xmldb_kalturavideo_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2011090901) {
        $file = $CFG->dirroot.'/mod/kalturavideo/db/install.xml';
        $dbman->install_one_table_from_xmldb_file($file, 'kalturaplayers');

        upgrade_plugin_savepoint(true, 2011090901, 'resource', 'kalturavideo');
    }
    if ($oldversion < 2011090902) {
        $table = new xmldb_table('kalturavideo');
        $field = new xmldb_field('kaltura_player', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, 'kalturavideo');
        $dbman->add_field($table, $field);

        upgrade_plugin_savepoint(true, 2011090902, 'resource', 'kalturavideo');
    }
    if ($oldversion < 2011091200) {
        $ids = get_config('local_kaltura', 'kaltura_selections');
        if (!empty($ids)) {
            $data = handleAction('getUiConfDetails', array('ids' => $ids));
            foreach ($data['objects'] as $ob) {
                $player       = new stdClass;
                $player->id   = $obid;
                $player->name = $obname;

                print $OUTPUT->box('Adding player ' . $player->id . ': "' . $player->name . '"');
                $DB->import_record('kalturaplayers', $player);
            }
        }
        upgrade_plugin_savepoint(true, 2011091200, 'resource', 'kalturavideo');
    }

    return true;
}
