<?php

namespace Kasperski\WorklogBundle\Repository\Write;

use Kasperski\WorklogBundle\Entity\Worklog;
use Symfony\Component\HttpFoundation\Session\Session;

class WorklogSessionWriteRepository
{
    const KEY = 'currentWorklog';

    /**
     * @var Session $session
     */
    private $session;

    /**
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function persist(Worklog $worklog)
    {
        $this->session->set(self::KEY, $worklog);
    }

    public function clear()
    {
        $this->session->remove(self::KEY);
    }
}