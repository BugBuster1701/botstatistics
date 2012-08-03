<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2012 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * BotStatistics - Cron
 *
 * PHP version 5
 * @copyright  Glen Langer 2012 
 * @author     Glen Langer 
 * @package    BotStatistics 
 * @license    LGPL 
 */


/**
 * Class BotStatisticsCron 
 *
 * @copyright  Glen Langer 2012 
 * @author     Glen Langer 
 * @package    BotStatistics
 */
class BotStatisticsCron extends Frontend
{
	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function deleteStatisticsData()
	{
	    $mindate = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d")-90, date("Y")));
	    
		$objCron = $this->Database->prepare("SELECT * FROM `tl_module` 
                                             WHERE `type`=? AND `botstatistics_cron`=?")
		                          ->execute('botstatistics', 1);
		while ($objCron->next())
		{
    	    $objStatDelete = $this->Database->prepare("DELETE FROM `tl_botstatistics_counter`, `tl_botstatistics_counter_details`
                                        	            USING `tl_botstatistics_counter`, `tl_botstatistics_counter_details`
                                        	            WHERE `tl_botstatistics_counter`.`id` = `tl_botstatistics_counter_details`.`pid`
    	                                                AND `tl_botstatistics_counter`.`bot_module_id`=?
                                        	            AND `tl_botstatistics_counter`.`bot_date`<?")
    	                                    ->execute($objCron->id, $mindate);
    	    // Add log entry
    	    $this->log('Deletion of old Botstatistics data for module '.$objCron->id, 'BotStatisticsCron deleteStatisticsData()', TL_CRON);
		}
	}
	
}//class

?>