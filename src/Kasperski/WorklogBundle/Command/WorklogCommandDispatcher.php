<?php

namespace Kasperski\WorklogBundle\Command;

use Kasperski\WorklogBundle\Command\Result\WorklogResult;
use Kasperski\WorklogBundle\Repository\Write\WorklogDoctrineWriteRepository;
use Kasperski\WorklogBundle\Repository\Write\WorklogSessionWriteRepository;

class WorklogCommandDispatcher {

    /**
     * @var WorklogSessionWriteRepository
     */
    protected $worklogSessionWriteRepository;

    /**
     * @var WorklogDoctrineWriteRepository
     */
    protected $worklogDoctrineWriteRepository;

    /**
     * @param WorklogSessionWriteRepository $worklogSessionWriteRepository
     * @param WorklogDoctrineWriteRepository $worklogDoctrineWriteRepository
     */
    public function __construct(
        WorklogSessionWriteRepository $worklogSessionWriteRepository,
        WorklogDoctrineWriteRepository $worklogDoctrineWriteRepository
    ) {
        $this->worklogSessionWriteRepository = $worklogSessionWriteRepository;
        $this->worklogDoctrineWriteRepository = $worklogDoctrineWriteRepository;
    }

    /**
     * @param CreateWorklogCommand $command
     * @return WorklogResult
     */
    public function dispatch(CreateWorklogCommand $command)
    {
        $worklog = $command->getWorklog();
        try {
            $this->worklogSessionWriteRepository->persist($worklog);
        } catch (\Exception $e) {
            return new WorklogResult(['error']);
        }

        return new WorklogResult();
    }

    /**
     * @param CreateWorklogCommand $command
     * @return WorklogResult
     */
    public function pause(CreateWorklogCommand $command)
    {
        $worklog = $command->getWorklog();
        $worklog->setPausedAt(new \DateTimeImmutable());
        try {
            $this->worklogSessionWriteRepository->persist($worklog);
        } catch (\Exception $e) {
            return new WorklogResult(['error']);
        }

        return new WorklogResult();
    }

    /**
     * @param CreateWorklogCommand $command
     * @return WorklogResult
     */
    public function resume(CreateWorklogCommand $command)
    {
        $worklog = $command->getWorklog();
        $worklog->setResumedAt(new \DateTimeImmutable());
        try {
            $this->worklogSessionWriteRepository->persist($worklog);
        } catch (\Exception $e) {
            return new WorklogResult(['error']);
        }

        return new WorklogResult();
    }

    /**
     * @param CreateWorklogCommand $command
     * @return WorklogResult
     */
    public function stop(CreateWorklogCommand $command)
    {
        $worklog = $command->getWorklog();
        $worklog->setStoppedAt(new \DateTimeImmutable());
        $worklog->measureTimeSpent();
        try {
            if ($this->worklogDoctrineWriteRepository->persist($worklog)) {
                $this->worklogSessionWriteRepository->clear();
            }
        } catch (\Exception $e) {
            return new WorklogResult(['error']);
        }

        return new WorklogResult();
    }
}
