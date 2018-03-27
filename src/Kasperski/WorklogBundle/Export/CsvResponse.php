<?php

namespace Kasperski\WorklogBundle\Export;

use Symfony\Component\HttpFoundation\Response;

class CsvResponse extends Response
{
    /**
     * @var array
     */
    protected $data;

    /**
     * @var string
     */
    protected $filename = 'export.csv';

    /**
     * CsvResponse constructor.
     * @param array $data
     * @param int $status
     * @param array $headers
     */
    public function __construct($data = array(), $status = 200, $headers = array())
    {
        parent::__construct('', $status, $headers);

        $this->setData($data);
    }

    /**
     * @param array $data
     * @return $this
     */
    public function setData(array $data)
    {
        $output = fopen('php://temp', 'r+');

        foreach ($data as $row) {
            $row = $this->convertDateTime($row);
            fputcsv($output, $row);
        }

        rewind($output);
        $this->data = '';
        while ($line = fgets($output)) {
            $this->data .= $line;
        }

        $this->data .= fgets($output);

        return $this->update();
    }

    /**
     * @param $row
     * @return mixed
     */
    private function convertDateTime($row)
    {
        foreach ($row as $key => $item) {
            if ($item instanceof \DateTime) {
                $row[$key] = $item->format('Y-m-d H:i:s');
            }
        }

        return $row;
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @param $filename
     * @return $this
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
        return $this->update();
    }

    /**
     * @return $this
     */
    protected function update()
    {
        $this->headers->set('Content-Disposition', sprintf('attachment; filename="%s"', $this->filename));

        if (!$this->headers->has('Content-Type')) {
            $this->headers->set('Content-Type', 'text/csv');
        }

        return $this->setContent($this->data);
    }
}
