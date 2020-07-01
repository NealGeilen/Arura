<?php
namespace Arura\Analytics;

use Arura\Exceptions\NotFound;
use Arura\Settings\Application;
use Exception;
use Google_Exception;
use Google_Service_AnalyticsReporting_DateRange;
use Google_Service_AnalyticsReporting_Dimension;
use Google_Service_AnalyticsReporting_GetReportsRequest;
use Google_Service_AnalyticsReporting_Metric;
use Google_Service_AnalyticsReporting_ReportRequest;

class ReportRequest extends Analytics {

    protected $metrics = [];
    protected $dimensions = [];
    protected $reportRequest;

    /**
     * ReportRequest constructor.
     * @throws NotFound
     * @throws Google_Exception
     */
    public function __construct()
    {
        $this->setReportRequest(new Google_Service_AnalyticsReporting_ReportRequest());
        parent::buildClient();
    }

    public function setDataRange($startDat,$endData){
        $dateRange = new Google_Service_AnalyticsReporting_DateRange();
        $dateRange->setStartDate($startDat);
        $dateRange->setEndDate($endData);
        $this->getReportRequest()->setDateRanges($dateRange);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getReport(){
        $this->getReportRequest()->setViewId(Application::get("analytics google", "Vieuw"));
        $this->getReportRequest()->setPageSize("10000");
        $this->getReportRequest()->setDimensions($this->getDimensions());
        $this->getReportRequest()->setMetrics($this->getMetrics());

        $body = new Google_Service_AnalyticsReporting_GetReportsRequest();
        $body->setReportRequests(array($this->getReportRequest()));

        try
        {
            // Initial validation.
            if ($this->getReporting() == null)
                throw new Exception("service");

            // Make the request.
            $aRecords = [];
            $report = $this->getReporting()->reports->batchGet($body)->getReports()[0];
            $aRecords["headers"]["dimensions"] = $report->columnHeader->dimensions;
            foreach ($report->columnHeader->metricHeader->metricHeaderEntries as $header){
                $aRecords["headers"]["metric"][] = $header->name;
            }
            foreach ($report->data->rows as $row){
                $aMetrics = [];
                foreach ($row->metrics as $metric){
                    $aMetrics = $metric->values;
                }
                $aRecords["rows"]["metrics"][] = $aMetrics[0];
                $aRecords["rows"]["dimensions"][]  = $row->dimensions[0];
            }

            return $aRecords;
        }
        catch (Exception $ex)
        {
            throw new Exception("Request Reports.BatchGet failed.". $ex->getMessage());
        }
    }

    /**
     * @return array
     */
    public function getMetrics(): array
    {
        return $this->metrics;
    }

    /**
     * @param array $metrics
     */
    public function addMetrics(Google_Service_AnalyticsReporting_Metric $metrics): void
    {
        $this->metrics[] = $metrics;
    }

    /**
     * @return array
     */
    public function getDimensions(): array
    {
        return $this->dimensions;
    }

    /**
     * @param array $dimensions
     */
    public function addDimensions(Google_Service_AnalyticsReporting_Dimension $dimensions): void
    {
        $this->dimensions[] = $dimensions;
    }

    /**
     * @return mixed
     */
    public function getReportRequest() : Google_Service_AnalyticsReporting_ReportRequest
    {
        return $this->reportRequest;
    }

    /**
     * @param mixed $reportRequest
     */
    protected function setReportRequest(Google_Service_AnalyticsReporting_ReportRequest $reportRequest): void
    {
        $this->reportRequest = $reportRequest;
    }
}