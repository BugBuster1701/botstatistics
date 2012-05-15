<?php
/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2012 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 * 
 * Modul BotStatistics Count - Frontend for Counting
 *
 * PHP version 5
 * @copyright  Glen Langer 2012
 * @author     Glen Langer 
 * @package    BotStatistics 
 * @license    LGPL 
 * @filesource
 */

/**
 * Initialize the system
 */
define('TL_MODE', 'FE');
require(dirname(dirname(dirname(__FILE__))).'/initialize.php');

/**
 * Class ModuleBotStatisticsCount 
 *
 * @copyright  Glen Langer 2012
 * @author     Glen Langer 
 * @package    BotStatistics
 * @license    LGPL 
 */
class ModuleBotStatisticsCount extends Frontend  
{
	private $_BOT = false; // Bot
	
	/**
	 * Initialize object 
	 */
	public function __construct()
	{
		parent::__construct();
	}

	public function run()
	{
        /* __________  __  ___   _____________   ________
          / ____/ __ \/ / / / | / /_  __/  _/ | / / ____/
         / /   / / / / / / /  |/ / / /  / //  |/ / / __
        / /___/ /_/ / /_/ / /|  / / / _/ // /|  / /_/ /
        \____/\____/\____/_/ |_/ /_/ /___/_/ |_/\____/ only
        */
		
		/*/
        TODO: All :-) 

		//*/
		//Pixel und raus hier
		header('Cache-Control: no-cache');
		header('Content-type: image/gif');
		header('Content-length: 43');

		echo base64_decode('R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==');
	} //function

} // class

/**
 * Instantiate counter
 */
$objModuleBotStatisticsCount = new ModuleBotStatisticsCount();
$objModuleBotStatisticsCount->run();

?>