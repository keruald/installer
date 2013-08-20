<?php

/**
 * The installer scenario class.
 *
 * (c) 2013, Keruald Project, some rights reserved.
 * Released under BSD license.
 *
 * @package     Keruald
 * @subpackage  Installer
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @copyright   2013 Keruald Project
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 */

/**
 * InstallerScenarioResult enum
 *
 * The result of an installer scenario
 */
class InstallerScenarioResult extends SplEnum {
    /**
     * Enum default value (OK)
     */
    const __default = self::OK;

    /**
     * The scenario have ran well.
     */
    const OK = 1;

    /**
     * The scenario requirements aren't satisfied.
     */
    const RequirementsFailure = 2;

    /**
     * At least one of the scenario task has failed.
     */
    const TaskFailure = 3;
}

/**
 * Installer scenario class
 *
 * Represents a collection of installation settings and tasks to perform.
 *
 * This class is intended to be serialized into and deserialized from a scenario file.
 */
class InstallerScenario {
    /**
     * The tasks to perform
     *
     * @var Array an array of InstallerTask instances
     */
    public $tasks;

    /**
     * The list of exceptions thrown by tasks during scenario run
     *
     * @var Array an array of InstallerTaskException instances
     */
    public $taskExceptions = array();

    /**
     * Should the scenario be stopped if a task fails?
     *
     * @var Boolean true if the scenario must be stopped on tasks failure ; otherwise, false.
     */
    public $stopOnTaskFailure = false;

    /**
     * Checks if the scenario requirements are met
     *
     * @return Boolean true if the requirements are met ; otherwise, false.
     */
    public function CheckRequirements () {
        return true;
    }

    /**
     * Runs the tasks
     *
     * @return InstallerScenarioResult the scerario result
     */
    public function Run () {
        //Checks requirement
        if (!$this->CheckRequirements()) {
            return InstallerScenarioResult::RequirementsFailure;
        }

        //Run tasks (until one fails if stopOnTaskFailure has been defined)
        $result = InstallerScenarioResult::OK;
        foreach ($this->tasks as $task) {
            try {
                $task->Run();
            } catch (InstallerTaskException $ex) {
                $result = InstallerScenarioResult::TaskFailure;
                $this->taskExceptions[] = $ex;
                if ($this->stopOnTaskFailure) break;
            }
        }

        return $result;
    }
}
