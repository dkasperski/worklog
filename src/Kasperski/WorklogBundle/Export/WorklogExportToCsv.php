<?php

namespace Kasperski\WorklogBundle\Export;

class WorklogExportToCsv extends WorklogExport {

    const WORKLOG_FILE_NAME = 'worklogs.csv';

    private $headers = [
        'Id', 'Nazwa użytkownika', 'Rozpoczęcie pracy', 'Zakończenie pracy', 'Przeznaczony czas', 'Komentarz'
    ];

    /**
     * @param $worklogs
     * @return CSVResponse
     */
    public function export($worklogs)
    {
        $response = new CsvResponse($worklogs, 200, $this->headers);
        $response->setFilename(self::WORKLOG_FILE_NAME);
        return $response;
    }
}
