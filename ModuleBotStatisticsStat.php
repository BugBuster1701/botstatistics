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
	 * Constructor
	 */
	public function __construct()
	{
	    parent::__construct();
	}
	
	/**
	 * Generate module
	 */
	protected function compile()
	{
		// Version
		require_once(TL_ROOT . '/system/modules/botstatistics/ModuleBotStatisticsVersion.php');
		
		$this->Template->href = $this->getReferer(true);
		$this->Template->title = specialchars($GLOBALS['TL_LANG']['MSC']['backBT']);
		$this->Template->button = $GLOBALS['TL_LANG']['MSC']['backBT'];
		
		$this->Template->botstatistics_version = $GLOBALS['TL_LANG']['MSC']['tl_botstatistics_stat']['modname'] . ' ' . BOTSTATISTICS_VERSION .'.'. BOTSTATISTICS_BUILD;
		
		


	}
	
	/**
	 * Timestamp nach Datum in deutscher oder internationaler Schreibweise
	 *
	 * @param	string		$language
	 * @param	insteger	$intTstamp
	 * @return	string
	 */
	protected function parseDateVisitors($language='en', $intTstamp=null)
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