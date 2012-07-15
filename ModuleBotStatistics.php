<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2012 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * Module BotStatistics - FE
 *
 * PHP version 5
 * @copyright  Glen Langer 2012 
 * @author     Glen Langer 
 * @package    BotStatistics 
 * @license    LGPL 
 */


/**
 * Class ModuleBotStatistics 
 *
 * @copyright  Glen Langer 2012 
 * @author     Glen Langer 
 * @package    BotStatistics
 */
class ModuleBotStatistics extends Module
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_botstatistics_fe';

	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
	    if (TL_MODE == 'BE')
	    {
	        $objTemplate = new BackendTemplate('be_wildcard');
	        $objTemplate->wildcard = '### BotStatistics Counter ###';
	        $objTemplate->title = $this->headline;
	        $objTemplate->id = $this->id;
	        $objTemplate->link = $this->name;
	        $objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;
	        return $objTemplate->parse();
	    }
	
	    return parent::generate();
	}
	
	/**
	 * Generate module
	 */
	protected function compile()
	{
	    //$this->import('Database');
	    //log_message('BotCountUpdate Aufruf mit ID: '.$this->id,'debug.log');
	    /*
	    $this->BotCountUpdate($this->id); // Modul ID
	    return;
	    */
	    global $objPage; // for alias
	    
	    $arrBotStatistics = array();
	    $arrBotStatistics['BotStatisticsID'] = $this->id;
	    $arrBotStatistics['PageAlias']       = $objPage->alias;
	    $this->Template->botstatistics = $arrBotStatistics;
	}
	
}//class

?>