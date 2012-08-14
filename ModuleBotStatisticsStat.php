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
class ModuleBotStatisticsStat extends BotStatisticsHelper
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
	    //act=zero&zid=...
	    if ($this->Input->get('act',true)=='zero') 
	    {
	        $this->setZero();
	    }
	    $this->checkExtensions('','be_main'); //for statistics page directly, callback modules use not the template hook
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
		        //fuer direkten Zugriff
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
	        $this->Template->BotSummary = $this->getBotStatSummary();
	    }

	} // compile
	
	/**
	 * Statistic, set on zero
	 */
	protected function setZero()
	{
	    if ( is_numeric($this->Input->get('zid')) && $this->Input->get('zid') > 0 ) 
	    {
	        $module_id = $this->Input->get('zid');
	    }
	    else 
	    {
	        return ; // wrong zid
	    }
	    $objStatDelete = $this->Database->prepare("DELETE FROM `tl_botstatistics_counter`, `tl_botstatistics_counter_details` 
                                                    USING `tl_botstatistics_counter`, `tl_botstatistics_counter_details` 
                                                    WHERE `tl_botstatistics_counter`.`id` = `tl_botstatistics_counter_details`.`pid`
                                                    AND `tl_botstatistics_counter`.`bot_module_id`=?")
                                        ->execute($module_id);
	    return ;
	}
	
}
?>