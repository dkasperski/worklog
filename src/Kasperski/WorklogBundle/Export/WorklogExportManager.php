<?php

namespace Kasperski\WorklogBundle\Export;

class WorklogExportManager extends ExportManager {

    const EXPORT_CSV = 'csv';

    /**
     * @var string
     */
    private $mode;

    public function __construct($mode)
    {
        $this->mode = $mode;
    }

    /**
     * @return WorklogExportToCsv
     */
    function getExportFormat()
    {
        switch ($this->mode) {
            case (self::EXPORT_CSV):
                return new WorklogExportToCsv();
            default:
                return new WorklogExportToCsv();
        }
    }
}
