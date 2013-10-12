<?php

/**
 * Contao Open Source CMS, Copyright (C) 2005-2013 Leo Feyer
 *
 * Module BotStatistics Stat - Backend
 * Botstatistic details
 *  
 * @copyright  Glen Langer 2012..2013 <http://www.contao.glen-langer.de>
 * @author     Glen Langer (BugBuster)
 * @package    BotStatistics
 * @license    LGPL
 * @filesource
 * @see        https://github.com/BugBuster1701/botstatistics
 */

/**
 * Run in a custom namespace, so the class can be replaced
 */
namespace BugBuster\BotStatistics;

/**
 * Initialize the system
 */
define('TL_MODE', 'BE');
require('../../../initialize.php');

/**
 * Class BotStatisticsDetails
 *
 * @copyright  Glen Langer 2012..2013 <http://www.contao.glen-langer.de>
 * @author     Glen Langer (BugBuster)
 * @package    BotStatistics
 */
class BotStatisticsDetails extends \BugBuster\BotStatistics\BotStatisticsHelper 
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
   	    if ( is_null( \Input::get('action',true) ) || 
   	         is_null( \Input::get('bmid',true) ) )
   	    {
   	        echo '<html><body><p class="tl_error">'.$GLOBALS['TL_LANG']['tl_botstatistics']['wrong_parameter'].'</p></body></html>';
            return ;
	    }
	    
	    switch (\Input::get('action',true))
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
	            $DetailFunction = 'getBotStatDetails'.\Input::get('action',true);
	            echo $this->$DetailFunction( \Input::get('action',true), \Input::get('bmid',true) );
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

