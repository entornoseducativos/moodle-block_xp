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
 * Admin config.
 *
 * @package    block_xp
 * @copyright  2017 Branch Up Pty Ltd
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\config;
defined('MOODLE_INTERNAL') || die();

use coding_exception;

/**
 * Admin config.
 *
 * Note that the settings page generated by Moodle core may not reflect
 * the values returned by this class, and this is because they do not fall
 * back on the defaults when values are not set, but we do. And we do it right.
 *
 * @package    block_xp
 * @copyright  2017 Branch Up Pty Ltd
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_config implements config {

    /** @var config The defaults. */
    protected $defaults;

    /**
     * Constructor.
     *
     * @param config $defaults The defaults.
     */
    public function __construct(config $defaults) {
        $this->defaults = $defaults;
    }

    /**
     * Get a value.
     *
     * @param string $name The name.
     * @return mixed
     */
    public function get($name) {
        $this->validate($name);

        // Legacy hack.
        $plugin = 'block_xp';
        $origname = $name;
        if ($name === 'context') {
            $plugin = 'core';
            $name = 'block_xp_context';
        }

        // In very rare situations we may not found the config value, this typically
        // occurs when the code runs before settings have been upgraded. Such as when
        // an admin does not visit the notifications page.
        // Also note that the comparison (=== false) is correct here because
        // get_config() returns strings.
        $value = get_config($plugin, $name);
        if ($value === false) {
            $value = $this->defaults->get($name);
        }
        return $value;
    }

    /**
     * Get all config.
     *
     * @return array
     */
    public function get_all() {
        global $CFG;
        $all = (array) get_config('block_xp');
        if (isset($CFG->block_xp_context)) {
            $all['context'] = $CFG->block_xp_context;
        }

        // Get the defaults.
        $configdefaults = $this->defaults->get_all();

        // Remove what we were not suppose to have.
        $cleaned = array_intersect_key($all, $configdefaults);

        // Make sure we return all the keys by including the defaults.
        return array_merge($configdefaults, $cleaned);
    }

    /**
     * Whether we have that config.
     *
     * @param string $name The config name.
     * @return bool
     */
    public function has($name) {
        return $this->defaults->has($name);
    }

    /**
     * Set a value.
     *
     * @param string $name Name of the config.
     * @param mixed $value The value.
     */
    public function set($name, $value) {
        $this->validate($name);

        // Legacy hack.
        $plugin = 'block_xp';
        if ($name === 'context') {
            $plugin = 'core';
            $name = 'block_xp_context';
        }

        set_config($name, $value, $plugin);
    }

    /**
     * Set many.
     *
     * @param array $values Keys are config names, and values are values.
     */
    public function set_many(array $values) {
        foreach ($values as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * Validate the config key.
     *
     * @param string $name The config key.
     * @return void
     * @throws coding_exception
     */
    protected function validate($name) {
        if (!$this->defaults->has($name)) {
            throw new coding_exception('Unknown config name.');
        }
    }

}
