<?php

namespace Kasperski\WorklogBundle\Repository\Read;

use Kasperski\WorklogBundle\Entity\Worklog;
use Kasperski\WorklogBundle\Exceptions\WorklogNotFoundException;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Translation\DataCollectorTranslator as Translator;

class WorklogSessionReadRepository
{
    const KEY = 'currentWorklog';

    /**
     * @var Session $session
     */
    private $session;

    /**
     * @var Translator $translator
     */
    private $translator;

    /**
     * WorklogSessionReadRepository constructor.
     * @param Session $session
     * @param Translator $translator
     */
    public function __construct(Session $session, Translator $translator)
    {
        $this->session = $session;
        $this->translator = $translator;
    }

    /**
     * @return Worklog
     * @throws WorklogNotFoundException
     */
    public function get()
    {
        if (!$this->session->has(self::KEY)) {
            throw new WorklogNotFoundException(
                $this->translator->trans('kasperski.worklog.not.found.exception')
            );
        }

        return $this->session->get(self::KEY);
    }

    /**
     * @return bool
     */
    public function has()
    {
        return $this->session->has(WorklogSessionReadRepository::KEY);
    }
}