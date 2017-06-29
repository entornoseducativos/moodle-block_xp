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
 * Main factory.
 *
 * @package    block_xp
 * @copyright  2017 Branch Up Pty Ltd
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\factory;
defined('MOODLE_INTERNAL') || die();

use moodle_database;
use block_xp\local\config\config;

/**
 * Main factory.
 *
 * @package    block_xp
 * @copyright  2017 Branch Up Pty Ltd
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class factory implements course_world_factory {

    /** @var config The admin config. */
    protected $adminconfig;
    /** @var moodle_database The DB. */
    protected $db;
    /** @var bool For the whole site? */
    protected $forwholesite = false;
    /** @var course_world[] World cache. */
    protected $worlds = [];

    /**
     * Constructor.
     *
     * @param config $adminconfig The admin config.
     * @param moodle_database $db The DB.
     */
    public function __construct(config $adminconfig, moodle_database $db) {
        $this->adminconfig = $adminconfig;
        $this->db = $db;
        if ($adminconfig->get('context') == CONTEXT_SYSTEM) {
            $this->forwholesite = true;
        }
    }

    /**
     * Get the world.
     *
     * @param int $courseid Course ID.
     * @return block_xp\local\course_world
     */
    public function get_world($courseid) {

        // When the block was set up for the whole site we attach it to the site course.
        if ($this->forwholesite) {
            $courseid = SITEID;
        }

        $courseid = intval($courseid);
        if (!isset($this->worlds[$courseid])) {
            $this->worlds[$courseid] = new \block_xp\local\course_world($this->adminconfig, $this->db, $courseid);
        }
        return $this->worlds[$courseid];
    }

}
