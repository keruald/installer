<?php

/**
 * The web installer wizard class.
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
 * Installer web installer wizard class
 *
 * Represents a collection of tasks to run, one by one, and the wizard output.
 */
abstract class WebInstallerWizard {
    //
    // Properties
    //

    /**
     * The tasks to perform
     *
     * @var Array an array of InstallerTask instances
     */
    public $tasks;

    /**
     * The current step;
     *
     * @var int
     */
    public $step = 0;


    //
    // Current step methods
    //

    /**
     * Runs the current task, and outputs the result.
     */
    public function RunCurrentTask () {
        try {
            $this->$tasks[$step]->Run();
        } catch (InstallerTaskException $ex) {
            //TODO: task failure HTML output
            return;
        }

        //TODO: task success HTML output
    }

    /**
     * Goes to the next step
     */
    public function NextStep () {
        $this->step++;
    }

    //
    // Scenario import/export
    //
    
    /**
     * Default installer scenario type
     */
    const DefaultInstallerScenarioType = 'InstallerScenario';

    /**
     * Exports a scenario
     *
     * @param string $installerScenarioType The type of the installer scenario (optional)
     * @return InstallerScenario The scenario based on the current settings
     *
     * @throws InvalidArgumentException if the type isn't a class name extending InstallerScenario
     */
    public function ExportScenario ($installerScenarioType = null) {
        if (!$installerScenarioType) $installerScenarioType = static::DefaultInstallerScenarioType;
        $reflectionClass = new ReflectionClass($installerScenarioType);
        if ($installerScenarioType != 'InstallerScenario' && !$reflectionClass->isSubclassOf('InstallerScenario')) {
            throw new InvalidArgumentException("$installerScenarioType isn't an subclass of InstallerScenario");
        }

        $scenario = $reflectionClass->newInstance();
        $scenario->tasks = $this->tasks;
        return $scenario;
    }

    /**
     * Imports a scenario
     *
     * @param InstallerScenario $scenario The scenario to import
     * @return WebInstallerWizard An instance of the wizard, matching the scenario.
     **/
    public static function ImportScenario ($scenario) {
        $wizard = new static();
        $wizard->tasks = $scenario->tasks;
        return $wizard;
    }
}