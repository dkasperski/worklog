<?php

namespace Kasperski\WorklogBundle\Command;

use Kasperski\WorklogBundle\Entity\Worklog;
use Kasperski\WorklogBundle\Repository\Read\WorklogSessionReadRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class WorklogCommandFactory {

    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    /**
     * @var WorklogSessionReadRepository
     */
    private $worklogSessionReadRepository;

    /**
     * @param TokenStorage $tokenStorage
     * @param WorklogSessionReadRepository $worklogSessionReadRepository
     */
    public function __construct(
        TokenStorage $tokenStorage,
        WorklogSessionReadRepository $worklogSessionReadRepository
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->worklogSessionReadRepository = $worklogSessionReadRepository;
    }

    /**
     * @return CreateWorklogCommand
     */
    public function create()
    {
        if (!$this->worklogSessionReadRepository->has()) {
            $worklog = $this->createWorklog(new \DateTimeImmutable());
        } else {
            $worklog = $this->worklogSessionReadRepository->get();
        }
        $worklogCommand = new CreateWorklogCommand($worklog);

        return $worklogCommand;
    }

    /**
     * @param \DateTimeImmutable $startDate
     * @return Worklog
     */
    protected function createWorklog(\DateTimeImmutable $startDate)
    {
        return new Worklog(
            $this->tokenStorage->getToken()->getUser(),
            $startDate
        );
    }
}
