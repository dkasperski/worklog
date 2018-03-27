<?php

namespace Kasperski\WorklogBundle\Export;

abstract class WorklogExport {

    /**
     * @param $data
     * @return mixed
     */
    abstract function export($data);
}
