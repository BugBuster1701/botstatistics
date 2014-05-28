<?php 

/**
 * Contao Open Source CMS, Copyright (C) 2005-2013 Leo Feyer
 *
 * @copyright  Glen Langer 2012..2013 <http://www.contao.glen-langer.de>
 * @author     Glen Langer (BugBuster)
 * @package    BotStatistics
 * @license    LGPL
 * @filesource
 * @see        https://github.com/BugBuster1701/botstatistics
 */

/**
 * Class BotStatisticsRunonce
 *
 * Runonce for BotStatistics
 *
 * @copyright  Glen Langer 2012..2013 <http://www.contao.glen-langer.de>
 * @author     Glen Langer (BugBuster)
 * @package    BotStatistics
 */
class BotStatisticsRunonce extends Controller
{
	public function __construct()
	{
	    parent::__construct();
	}
	public function run()
	{
	    if (is_file(TL_ROOT . '/system/modules/botstatistics/config/database.sql'))
	    {
	        $objFile = new File('system/modules/botstatistics/config/database.sql');
	        $objFile->delete();
	        $objFile->close();
    		$objFile=null;
    		unset($objFile);
	    }
	}
}

$BotStatisticsRunonce = new BotStatisticsRunonce();
$BotStatisticsRunonce->run();
