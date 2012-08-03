<?php
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
 * Initialize the system
 */
define('TL_MODE', 'BE');
require('../../initialize.php');

/**
 * Class BotStatisticsDetails
 *
 * @copyright  Glen Langer 2012
 * @author     Glen Langer
 * @package    BotStatistics
 */
class BotStatisticsDetails extends BotStatisticsHelper 
{
   
    /**
	 * Set the current file
	 */
	public function __construct()
	{
		$this->import('BackendUser', 'User');
		parent::__construct(); 
		$this->User->authenticate(); 
	    $this->loadLanguageFile('default');
		$this->loadLanguageFile('tl_botstatistics'); 
	}
	
    public function run()
	{
   	    if ( is_null( $this->Input->get('action',true) ) || 
   	         is_null( $this->Input->get('bmid',true) ) )
   	    {
   	        echo '<html><body><p class="tl_error">'.$GLOBALS['TL_LANG']['tl_botstatistics']['wrong_parameter'].'</p></body></html>';
            return ;
	    }
	    
	    switch ($this->Input->get('action',true))
	    {
	        case 'AnzBot' :
	        case 'AnzVisits' :
	        case 'AnzPages' :
	        case 'AnzBotToday' :
	        case 'AnzVisitsToday' :
	        case 'AnzPagesToday' :
	        case 'AnzBotYesterday' :
	        case 'AnzVisitsYesterday' :
	        case 'AnzPagesYesterday':
	        case 'AnzBotWeek' :
	        case 'AnzVisitsWeek' :
	        case 'AnzPagesWeek' :
	        case 'AnzBotLastWeek' :
	        case 'AnzVisitsLastWeek' :
	        case 'AnzPagesLastWeek' :
	            $DetailFunction = 'getBotStatDetails'.$this->Input->get('action',true);
	            echo $this->$DetailFunction( $this->Input->get('action',true), $this->Input->get('bmid',true) );
	            break;
	        default:
	            echo '<html><body><p class="tl_error">'.$GLOBALS['TL_LANG']['tl_botstatistics']['wrong_parameter'].'</p></body></html>';
	            break;
	    }   
	    return ;
	} // run
}

/**
 * Instantiate
 */
$objBotStatisticsDetails = new BotStatisticsDetails();
$objBotStatisticsDetails->run();

?>