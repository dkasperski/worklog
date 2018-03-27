<?php

namespace Kasperski\WorklogBundle\Export;

class WorklogHistoryExportFactory {

    /**
     * @param WorklogExportManager $worklogExportManager
     */
    public function __construct(WorklogExportManager $worklogExportManager)
    {
        $this->worklogExportManager = $worklogExportManager;
    }

    /**
     * @return mixed
     */
    public function create()
    {
        return $this->getExportFormat();
    }

    /**
     * @return mixed
     */
    protected function getExportFormat()
    {
        return $this->worklogExportManager->getExportFormat();;
    }
}