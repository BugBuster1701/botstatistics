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
     */
    protected function getBotStatBots()
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
    }
    
    /**
     * Fill Templatevars with Bot statistics order by date
     */
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
    }
    
} // class
