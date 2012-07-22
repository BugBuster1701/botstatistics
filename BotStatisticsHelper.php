<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2012 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 * 
 * Modul BotStatistics Stat - Helper 
 * 
 * PHP version 5
 * @copyright  Glen Langer 2012
 * @author     Glen Langer
 * @package    BotStatistics
 * @license    LGPL
 */


/**
 * Class BotStatisticsHelper
 *
 * @copyright  Glen Langer 2012
 * @author     Glen Langer
 * @package    BotStatistics
 */
class BotStatisticsHelper extends BackendModule
{
    /**
	 * Current object instance
	 * @var object
	 */
    protected static $instance = null;
    
    protected function compile()
    {
        
    }
    /**
     * Return the current object instance (Singleton)
     * @return BotStatisticsHelper
     */
    public static function getInstance()
    {
        if (self::$instance == null)
        {
            self::$instance = new BotStatisticsHelper();
        }
    
        return self::$instance;
    }

    /**
     * Hook: Check the required extensions and files for BotStatistics
     *
     * @param string $strContent
     * @param string $strTemplate
     * @return string
     */
    public function checkExtensions($strContent, $strTemplate)
    {
        if ($strTemplate == 'be_main')
        {
            if (!is_array($_SESSION["TL_INFO"]))
            {
                $_SESSION["TL_INFO"] = array();
            }
    
            // required extensions
            $arrRequiredExtensions = array(
                    'BotDetection' => 'botdetection'
            );
    
            // required files
            $arrRequiredFiles = array(
                    'Modulname' => 'plugins/.....'
            );
    
            // check for required extensions
            foreach ($arrRequiredExtensions as $key => $val)
            {
                if (!in_array($val, $this->Config->getActiveModules()))
                {
                    $_SESSION["TL_INFO"] = array_merge($_SESSION["TL_INFO"], array($val => 'Please install the required extension <strong>' . $key . '</strong>'));
                }
                else
                {
                    if (is_array($_SESSION["TL_INFO"]) && key_exists($val, $_SESSION["TL_INFO"]))
                    {
                        unset($_SESSION["TL_INFO"][$val]);
                    }
                }
            }
    
            // check for required files
            /*
            foreach ($arrRequiredFiles as $key => $val)
            {
                if (!file_exists(TL_ROOT . '/' . $val))
                {
                    $_SESSION["TL_INFO"] = array_merge($_SESSION["TL_INFO"], array($val => 'Please install the required file/extension <strong>' . $key . '</strong>'));
                }
                else
                {
                    if (is_array($_SESSION["TL_INFO"]) && key_exists($val, $_SESSION["TL_INFO"]))
                    {
                        unset($_SESSION["TL_INFO"][$val]);
                    }
                }
            }*/
        }
    
        return $strContent;
    } // checkExtension
    
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
    
    /**
     * Fill Templatevars with Bot statistics order by name
     *//*
    protected function getBotStatBots()
    {
        //Anzahl Bots mit Namen
        $objBotStatNameCount = $this->Database->prepare("SELECT DISTINCT `bot_name`"
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
                $arrBotStats[$objBotStatNameCount->bot_name][] = array($objBotStatNameCount->bot_name
                        ,$this->parseDateBots($GLOBALS['TL_LANGUAGE'],strtotime($objBotStat->bot_date))
                        ,$objBotStat->bot_counter);
            }
        }
        if ($intBotStatNameCount > 0)
        {
            $this->Template->bot_names = $arrBotNames;
            $this->Template->bot_stats = $arrBotStats;
        }
        return ;
    }*/
    
    /**
     * Fill Templatevars with Bot statistics order by date
     *//*
    protected function getBotStatDate()
    {
        //Anzahl Bots mit Datum
        $objBotStatDateCount = $this->Database->prepare("SELECT DISTINCT `bot_date`"
                                                      . " FROM `tl_botstatistics_counter`"
                                                      . " WHERE `bid`=?"
                                                      . " ORDER BY `bot_date` DESC")
                                              ->execute($this->intModuleID);
        $intBotStatDateCount = $objBotStatDateCount->numRows;
        $this->Template->bot_stat_date_count = $intBotStatDateCount;
        while ($objBotStatDateCount->next())
        {
            $arrBotDates[] = $objBotStatDateCount->bot_date;
        
            $objBotStat = $this->Database->prepare("SELECT `bot_date`, `bot_name`, `bot_counter`"
                                                 . " FROM `tl_botstatistics_counter`"
                                                 . " WHERE `bid`=? AND `bot_date`=?"
                                                 . " ORDER BY `bot_name` ASC")
                                         ->execute($this->intModuleID,$objBotStatDateCount->bot_date);
            while ($objBotStat->next())
            {
                $arrBotStats[$objBotStatDateCount->bot_date][] = array(
                         $this->parseDateBots($GLOBALS['TL_LANGUAGE'],strtotime($objBotStat->bot_date))
                        ,$objBotStat->bot_name
                        ,$objBotStat->bot_counter);
            }
        }
        if ($intBotStatDateCount > 0)
        {
            $this->Template->bot_stats_date = $arrBotStats;
        }
        return ;
    }*/
    
    protected function getBotStatSummary()
    {
        $today     = date('Y-m-d');
        $yesterday = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d")-1, date("Y")));

        $this->TemplatePartial = new BackendTemplate('mod_botstatistics_be_stat_partial_summary');
        
        $this->TemplatePartial->AnzBotYesterday    = 0;
        $this->TemplatePartial->AnzVisitsYesterday = 0;
        $this->TemplatePartial->AnzPagesYesterday  = 0;
        $this->TemplatePartial->AnzBotToday        = 0;
        $this->TemplatePartial->AnzVisitsToday     = 0;
        $this->TemplatePartial->AnzPagesToday      = 0;
        
        //Anzahl der Bots mit Summe Besuche und Seitenzugriffe
        $objBotStatCount = $this->Database->prepare("SELECT count(distinct `bot_name`) AS AnzBot, 
                                                    (SELECT sum(`bot_counter`) 
                                                     FROM `tl_botstatistics_counter` 
                                                     WHERE `bot_module_id`=?
                                                    ) AS AnzVisits
                                                    FROM `tl_botstatistics_counter` 
                                                    WHERE `bot_module_id`=?")
                                          ->execute($this->intModuleID, $this->intModuleID);
        $this->TemplatePartial->AnzBot    = $objBotStatCount->AnzBot;
        $this->TemplatePartial->AnzVisits = ($objBotStatCount->AnzVisits) ? $objBotStatCount->AnzVisits : 0;
        //Anzahl Seitenzugriffe
        $objBotStatCount = $this->Database->prepare("SELECT sum(`bot_page_alias_counter`) AS AnzPages
                                                     FROM `tl_botstatistics_counter_details` d
                                                     INNER JOIN `tl_botstatistics_counter` c ON d.pid = c.id
                                                     WHERE c.`bot_module_id`=?")
                                          ->execute($this->intModuleID);
        $this->TemplatePartial->AnzPages = ($objBotStatCount->AnzPages) ? $objBotStatCount->AnzPages : 0;
        
        //Anzahl Bots Heute/Gestern Besuche/Hits 
        $objBotStatCount = $this->Database->prepare("SELECT `bot_date`, count(distinct `bot_name`) AS AnzBot
                                                    , sum(`bot_counter`) AS AnzVisits
                                                    FROM `tl_botstatistics_counter`
                                                    WHERE `bot_module_id`=?
                                                    AND `bot_date`>=?
                                                    GROUP BY `bot_date`")
                                          ->execute($this->intModuleID, $yesterday);
        while ($objBotStatCount->next())
        {
            if ($objBotStatCount->bot_date == $yesterday)
            {
                $this->TemplatePartial->AnzBotYesterday    = $objBotStatCount->AnzBot;
                $this->TemplatePartial->AnzVisitsYesterday = $objBotStatCount->AnzVisits;
                
            }
            if ($objBotStatCount->bot_date == $today) 
            { 
                $this->TemplatePartial->AnzBotToday    = $objBotStatCount->AnzBot;
                $this->TemplatePartial->AnzVisitsToday = $objBotStatCount->AnzVisits;
            }
        }
        // Anzahl Seiten Gesamt - Heute/Gestern
        $objBotStatCount = $this->Database->prepare("SELECT `bot_date`, sum(`bot_page_alias_counter`) AS AnzPages 
                                                    FROM `tl_botstatistics_counter`
                                                    INNER JOIN `tl_botstatistics_counter_details`
                                                    ON `tl_botstatistics_counter`.id=`tl_botstatistics_counter_details`.pid
                                                    WHERE `bot_module_id`=? 
                                                    AND `bot_date`>=?
                                                    GROUP BY `bot_date`")
                                ->execute($this->intModuleID, $yesterday);
        while ($objBotStatCount->next())
        {
            if ($objBotStatCount->bot_date == $yesterday)
            {
                $this->TemplatePartial->AnzPagesYesterday = $objBotStatCount->AnzPages;
        
            }
            if ($objBotStatCount->bot_date == $today)
            {
                $this->TemplatePartial->AnzPagesToday = $objBotStatCount->AnzPages;
            }
        }
        
        //Anzahl Besuche aktuelle Woche, letzte Woche
        $this->TemplatePartial->AnzBotWeek    = 0;
        $this->TemplatePartial->AnzVisitsWeek = 0;
        $this->TemplatePartial->AnzPagesWeek  = 0;
        $this->TemplatePartial->AnzBotLastWeek    = 0;
        $this->TemplatePartial->AnzVisitsLastWeek = 0;
        $this->TemplatePartial->AnzPagesLastWeek  = 0;

        $CurrentWeek     = date('W'); 
        $LastWeek        = date('W', mktime(0, 0, 0, date("m"), date("d")-7, date("Y")) );
        //Besonderheit beachten das der 1.1. die 53. Woche sein kann!
        $YearCurrentWeek = ($CurrentWeek > 40 && (int)date('m') == 1) ? date('Y')-1 : date('Y');
        $YearLastWeek    = ($LastWeek    > 40 && (int)date('m') == 1) ? date('Y')-1 : date('Y');
        $objBotStatCount = $this->Database->prepare("SELECT YEARWEEK( `bot_date`, 3 ) AS YW
                                                    , COUNT(DISTINCT `bot_name`) AS AnzBotWeek 
                                                    , SUM(`bot_counter`) AS AnzVisitsWeek 
                                                    FROM `tl_botstatistics_counter`
                                                    WHERE `bot_module_id`=?
                                                    AND YEARWEEK( `bot_date`, 3 ) BETWEEN ? AND ?
                                                    GROUP BY YW
                                                    ORDER BY YW DESC")
                                ->execute($this->intModuleID, $YearLastWeek.$LastWeek, $YearCurrentWeek.$CurrentWeek);
        while ($objBotStatCount->next())
        {
            if ($objBotStatCount->YW == $YearCurrentWeek.$CurrentWeek)
            {
                $this->TemplatePartial->AnzBotWeek    = $objBotStatCount->AnzBotWeek;
                $this->TemplatePartial->AnzVisitsWeek = $objBotStatCount->AnzVisitsWeek;
            
            }
            if ($objBotStatCount->YW == $YearLastWeek.$LastWeek)
            {
                $this->TemplatePartial->AnzBotLastWeek    = $objBotStatCount->AnzBotWeek;
                $this->TemplatePartial->AnzVisitsLastWeek = $objBotStatCount->AnzVisitsWeek;
            }
        }
        //Anzahl Hits aktuelle, letzte Woche
        $objBotStatCount = $this->Database->prepare("SELECT YEARWEEK( c.`bot_date`, 3 ) AS YW, sum(d.`bot_page_alias_counter`) AS AnzPages
                                                    FROM `tl_botstatistics_counter` c
                                                    INNER JOIN  `tl_botstatistics_counter_details` d ON c.id=d.pid
                                                    WHERE c.`bot_module_id`=?
                                                    AND YEARWEEK( c.`bot_date`, 3 ) BETWEEN ? AND ?
                                                    GROUP BY YW
                                                    ORDER BY YW DESC")
                                          ->execute($this->intModuleID, $YearLastWeek.$LastWeek, $YearCurrentWeek.$CurrentWeek);
        while ($objBotStatCount->next())
        {
            if ($objBotStatCount->YW == $YearCurrentWeek.$CurrentWeek)
            {
                $this->TemplatePartial->AnzPagesWeek = $objBotStatCount->AnzPages;
        
            }
            if ($objBotStatCount->YW == $YearLastWeek.$LastWeek)
            {
                $this->TemplatePartial->AnzPagesLastWeek = $objBotStatCount->AnzPages;
            }
        }
        
        return $this->TemplatePartial->parse();
    }
    
} // class
