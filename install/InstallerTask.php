<?php

/**
 * The installer task class.
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
 * Installer task class
 *
 * Represents a task of the installer, like download software,
 * import a SQL schema or write a config file.
 */
abstract class InstallerTask {
    /**
     * Runs the task
     *
     * @throws InstallerTaskException if an error occured on task run
     */
    abstract protected function Run ();
}


/**
 * Installer task exception class
  */
class InstallerTaskException extends Exception { }