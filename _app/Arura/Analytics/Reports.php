<?php
namespace Arura\Analytics;

use Arura\Exceptions\NotFound;
use Exception;
use Google_Exception;
use Google_Service_AnalyticsReporting_Dimension;
use Google_Service_AnalyticsReporting_Metric;

class Reports{

    /**
     * @param $dimension
     * @param $metric
     * @return ReportRequest
     * @throws Google_Exception
     * @throws NotFound
     */
    protected static function Report($dimension, $metric){
        $Report = new ReportRequest();
        $date = new Google_Service_AnalyticsReporting_Dimension();
        $date->setName($dimension);
        $Report->addDimensions($date);
        $sessions = new Google_Service_AnalyticsReporting_Metric();
        $sessions->setExpression($metric);
        $sessions->setAlias($metric);
        $Report->addMetrics($sessions);
        return $Report;
    }

    /**
     * @param $startDate
     * @param $endDate
     * @return array
     * @throws NotFound
     * @throws Google_Exception
     * @throws Exception
     */
    public static function VistorsPerCountry($startDate,$endDate){
        $Report = self::Report("ga:countryIsoCode", "ga:sessions");
        $Report->setDataRange($startDate, $endDate);
        return $Report->getReport();
    }

    /**
     * @param $startDate
     * @param $endDate
     * @return array
     * @throws NotFound
     * @throws Google_Exception
     * @throws Exception
     */
    public static function VistorsPerCity($startDate,$endDate){
        $Report = self::Report("ga:city", "ga:sessions");
        $Report->setDataRange($startDate, $endDate);
        return $Report->getReport();
    }

    /**
     * @param $startDate
     * @param $endDate
     * @return array
     * @throws NotFound
     * @throws Google_Exception
     * @throws Exception
     */
    public static function VistorsPerProvince($startDate,$endDate){
        $Report = self::Report("ga:region", "ga:sessions");
        $Report->setDataRange($startDate, $endDate);
        return $Report->getReport();
    }


    /**
     * @param $startDate
     * @param $endDate
     * @return array
     * @throws Google_Exception
     * @throws NotFound
     * @throws Exception
     */
    public static function SocialMediaVisitors($startDate,$endDate){
        $Report = self::Report("ga:sourceMedium", "ga:sessions");
        $Report->setDataRange($startDate, $endDate);
        return $Report->getReport();
    }

    /**
     * @param $startDate
     * @param $endDate
     * @return array
     * @throws Google_Exception
     * @throws NotFound
     * @throws Exception
     */
    public static function ExitPages($startDate,$endDate){
        $Report = self::Report("ga:exitPagePath", "ga:sessions");
        $Report->setDataRange($startDate, $endDate);
        return $Report->getReport();
    }

    /**
     * @param $startDate
     * @param $endDate
     * @return array
     * @throws Google_Exception
     * @throws NotFound
     * @throws Exception
     */
    public static function PageViews($startDate,$endDate){
        $Report = self::Report("ga:pagePath", "ga:pageviews");
        $Report->setDataRange($startDate, $endDate);
        return $Report->getReport();
    }

    /**
     * @param $startDate
     * @param $endDate
     * @return array
     * @throws Google_Exception
     * @throws NotFound
     * @throws Exception
     */
    public static function ReadTimePage($startDate,$endDate){
        $Report = self::Report("ga:pageTitle", "ga:avgTimeOnPage");
        $Report->setDataRange($startDate, $endDate);
        return $Report->getReport();
    }

    /**
     * @param $startDate
     * @param $endDate
     * @return array
     * @throws Google_Exception
     * @throws NotFound
     * @throws Exception
     */
    public static function UserAge($startDate,$endDate){
        $Report = self::Report("ga:userAgeBracket", "ga:sessions");
        $Report->setDataRange($startDate, $endDate);
        return $Report->getReport();
    }

    /**
     * @param $startDate
     * @param $endDate
     * @return array
     * @throws Google_Exception
     * @throws NotFound
     * @throws Exception
     */
    public static function Devices($startDate,$endDate){
        $Report = self::Report("ga:deviceCategory", "ga:sessions");
        $Report->setDataRange($startDate, $endDate);
        return $Report->getReport();
    }

    public static function Visitors($startDate,$endDate){
        $Report = self::Report("ga:date", "ga:sessions");
        $Report->setDataRange($startDate, $endDate);
        $aData = $Report->getReport();

        foreach ($aData["rows"]["dimensions"] as $i => $data){
            $data = substr_replace($data, "-", 4, 0);
            $data = substr_replace($data, "-", 7, 0);
            $aData["rows"]["dimensions"][$i] = (date("d-m-Y",strtotime($data)));
        }

        return $aData;
    }




}