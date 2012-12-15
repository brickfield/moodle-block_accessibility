<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


/**
 * Defines upgrades for Accessibility Block
 *
 * @package   block_accessibility                                      (4)
 * @copyright Copyright 2009 onwards Taunton's College                   (5)
 * @author Mark Johnson <mark.johnson@tauntons.ac.uk>                  (6)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later (7)
 */
defined('MOODLE_INTERNAL') || die();

function xmldb_block_accessibility_upgrade($oldversion=0) {

    global $CFG, $THEME, $DB;

    $dbman = $DB->get_manager();

    $result = true;

    // And upgrade begins here. For each one, you'll need one
    // block of code similar to the next one. Please, delete
    // this comment lines once this file start handling proper
    // upgrade code.

    if ($result && $oldversion < 2009071000) {

        // Changing type of field fontsize on table accessibility to number
        $table = new XMLDBTable('accessibility');
        $field = new XMLDBField('fontsize');
        $field->setAttributes(XMLDB_TYPE_NUMBER,
                              '4, 1',
                              XMLDB_UNSIGNED,
                              null,
                              null,
                              null,
                              null,
                              null,
                              'userid');

        // Launch change of type for field fontsize
        $result = $result && $dbman->change_field_type($table, $field);
        upgrade_block_savepoint(true, 2009071000, 'accessibility');
    }

    if ($result && $oldversion < 2009082500) {

        // Define field colourscheme to be added to accessibility
        $table = new xmldb_table('accessibility');
        $field = new xmldb_field('colourscheme',
                              XMLDB_TYPE_INTEGER,
                              '1',
                              XMLDB_UNSIGNED,
                              null,
                              null,
                              null,
                              null,
                              null,
                              'fontsize');

        // Launch add field colourschemea
        if (!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_block_savepoint(true, 2009082500, 'accessibility');
    }

    if ($oldversion < 2010121500) {

        // Define field autoload_atbar to be added to accessibility
        $table = new xmldb_table('accessibility');
        $cs = new xmldb_field('colourscheme',
                              XMLDB_TYPE_INTEGER,
                              '1',
                              XMLDB_UNSIGNED,
                              null,
                              null,
                              null,
                              null,
                              null,
                              'fontsize');
        $field = new xmldb_field('autoload_atbar',
                                 XMLDB_TYPE_INTEGER,
                                 '1',
                                 XMLDB_UNSIGNED,
                                 XMLDB_NOTNULL,
                                 null,
                                 '0',
                                 'colourscheme');
        if (!$dbman->field_exists($table, $cs)) {
            $dbman->add_field($table, $cs);
        }
        // Conditionally launch add field autoload_atbar
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // accessibility savepoint reached
        upgrade_block_savepoint(true, 2010121500, 'accessibility');
    }

    if ($oldversion < 2011122000) {

        // Define table accessibility to be renamed to block_accessibility
        $table = new xmldb_table('accessibility');

        // Launch rename table for accessibility
        $dbman->rename_table($table, 'block_accessibility');

        // accessibility savepoint reached
        upgrade_block_savepoint(true, 2011122000, 'accessibility');
    }


    return $result;

}
