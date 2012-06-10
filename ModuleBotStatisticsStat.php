<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');
/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2012 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 * 
 * Modul BotStatistics Stat - Backend
 * 
 * PHP version 5
 * @copyright  Glen Langer 2012
 * @author     Glen Langer
 * @package    BotStatistics
 * @license    LGPL
 */


/**
 * Class ModuleBotStatisticsStat
 *
 * @copyright  Glen Langer 2012
 * @author     Glen Langer
 * @package    BotStatistics
 */
class ModuleBotStatisticsStat extends BackendModule
{
    /**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_botstatistics_be_stat';
	
	/**
	 * Module ID
	 * @var int
	 */
	protected $intModuleID;
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
	    parent::__construct();
	    
	    $this->intModuleID = (int)$this->Input->post('bot_module_id'); //Modul-ID
	}
	
	/**
	 * Generate module
	 */
	protected function compile()
	{
		// Version
		require_once(TL_ROOT . '/system/modules/botstatistics/ModuleBotStatisticsVersion.php');
		
		$this->Template->href   = $this->getReferer(true);
		$this->Template->title  = specialchars($GLOBALS['TL_LANG']['MSC']['backBT']);
		$this->Template->button = $GLOBALS['TL_LANG']['MSC']['backBT'];
		$this->Template->theme  = $this->getTheme();
		$this->Template->theme0 = 'default';
		$this->Template->bot_base    = $this->Environment->base;
		$this->Template->bot_base_be = $this->Environment->base . 'contao';
		
		if ($this->intModuleID == 0) 
		{   //direkter Aufruf ohne ID
		    $objBotModuleID = $this->Database->prepare("SELECT MIN(id) AS MID from tl_module WHERE `type`='botstatistics'")->execute();
		    $objBotModuleID->next();
		    if ($objBotModuleID->MID !== null) 
		    {
		        $this->intModuleID = $objBotModuleID->MID;
		    }
		}
		$this->Template->bot_module_id = $this->intModuleID;
		
		
		$this->Template->botstatistics_version = $GLOBALS['TL_LANG']['MSC']['tl_botstatistics_stat']['modname'] . ' ' . BOTSTATISTICS_VERSION .'.'. BOTSTATISTICS_BUILD;

		//Modul Namen holen
		$objBotModules = $this->Database->prepare("SELECT `id`, `botstatistics_name`"
                                            		. " FROM `tl_module`"
                                            		. " WHERE `type`='botstatistics'"
		                                            . " ORDER BY `botstatistics_name`")
                                        ->execute();
		$intBotModules = $objBotModules->numRows;
		if ($intBotModules > 0)
		{
		    while ($objBotModules->next())
		    {
		        $arrBotModules[] = array
		        (
                    'id'    => $objBotModules->id,
                    'title' => $objBotModules->botstatistics_name
		        );
		        $arrBotModules2[$objBotModules->id] = $objBotModules->botstatistics_name;
		    }
		}
		else 
	    { // es gibt kein Modul
    	    $arrBotModules[] = array
    	    (
                'id'    => '0',
                'title' => '---------'
    	    );
	    }
	    $this->Template->bot_modules = $arrBotModules;
	    $this->Template->bot_modules2 = $arrBotModules2;
	    
	    //Modul Werte holen
	    if ($intBotModules > 0)
	    {
	        //Anzahl Bots mit Namen
	        $objBotStatNameCount = $this->Database->prepare("SELECT  DISTINCT `bot_name`"
                                                      . " FROM `tl_botstatistics_counter`"
                                                      . " WHERE `bid`=?"
	                                                  . " ORDER BY `bot_name`")
        	                                  ->execute($this->intModuleID);
	        $intBotStatNameCount = $objBotStatNameCount->numRows;
	        $this->Template->bot_stat_name_count = $intBotStatNameCount;
	        while ($objBotStatNameCount->next())
	        {
                $arrBotNames[] = $objBotStatNameCount->bot_name;

    	        $objBotStat = $this->Database->prepare("SELECT `bot_date`, `bot_name`, `bot_counter`"
                                	                . " FROM `tl_botstatistics_counter`"
                                	                . " WHERE `bid`=? AND `bot_name`=?"
    	                                            . " ORDER BY `bot_date` DESC")
                                	         ->execute($this->intModuleID,$objBotStatNameCount->bot_name);
    	        while ($objBotStat->next())
    	        {
    	            //$arrBotStats[$objBotStatNameCount->bot_name][$objBotStat->bot_date] = $objBotStat->bot_counter;
    	            $arrBotStats[$objBotStatNameCount->bot_name][] = array($objBotStatNameCount->bot_name,$objBotStat->bot_date,$objBotStat->bot_counter);
    	        }
	        }
	        if ($intBotStatNameCount > 0) 
	        {
	            $this->Template->bot_names = $arrBotNames;
	            $this->Template->bot_stats = $arrBotStats;
	        }
	    }


	}
	
	/**
	 * Timestamp nach Datum in deutscher oder internationaler Schreibweise
	 *
	 * @param	string		$language
	 * @param	insteger	$intTstamp
	 * @return	string
	 */
	protected function parseDateBots($language='en', $intTstamp=null)
	{
		if ($language == 'de') 
		{
			$strModified = 'd.m.Y';
		} 
		else 
		{
			$strModified = 'Y-m-d';
		}
		if (is_null($intTstamp))
		{
			$strDate = date($strModified);
		}
		elseif (!is_numeric($intTstamp))
		{
			return '';
		}
		else
		{
			$strDate = date($strModified, $intTstamp);
		}
		return $strDate;
	}

	
}
?>