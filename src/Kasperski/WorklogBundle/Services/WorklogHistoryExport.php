<?php

namespace Kasperski\WorklogBundle\Services;

use Kasperski\WorklogBundle\Export\WorklogHistoryExportFactory;
use Kasperski\WorklogBundle\Repository\Read\WorklogDoctrineReadRepository;

class WorklogHistoryExport {

    /**
     * @var WorklogHistoryExportFactory
     */
    protected $worklogHistoryExportFactory;

    /**
     * @var WorklogDoctrineReadRepository
     */
    protected $worklogDoctrineReadRepository;

    /**
     * @param WorklogHistoryExportFactory $worklogHistoryExportFactory
     * @param WorklogDoctrineReadRepository $worklogDoctrineReadRepository
     */
    public function __construct(
        WorklogHistoryExportFactory $worklogHistoryExportFactory,
        WorklogDoctrineReadRepository $worklogDoctrineReadRepository
    ) {
        $this->worklogHistoryExportFactory = $worklogHistoryExportFactory;
        $this->worklogDoctrineReadRepository = $worklogDoctrineReadRepository;
    }

    /**
     * @param string|null $startedAt
     * @param string|null $stoppedAt
     * @return mixed
     */
    public function export($startedAt, $stoppedAt)
    {
        $worklogs = $this->worklogDoctrineReadRepository->getForCurrentUserExport($startedAt, $stoppedAt);
        $worklogExportFormat = $this->worklogHistoryExportFactory->create();
        return $worklogExportFormat->export($worklogs);
    }
}
