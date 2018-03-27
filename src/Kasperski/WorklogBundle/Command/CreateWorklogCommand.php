<?php

namespace Kasperski\WorklogBundle\Command;

use Kasperski\WorklogBundle\Entity\Worklog;

class CreateWorklogCommand {

    /**
     * @var Worklog
     */
    protected $worklog;

    /**
     * @param Worklog $worklog
     */
    public function __construct(Worklog $worklog)
    {
        $this->worklog = $worklog;
    }

    /**
     * @return Worklog
     */
    public function getWorklog()
    {
        return $this->worklog;
    }
}
