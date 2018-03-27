<?php

namespace Kasperski\WorklogBundle\Export;

abstract class ExportManager {

    /**
     * @return mixed
     */
    abstract function getExportFormat();
}
