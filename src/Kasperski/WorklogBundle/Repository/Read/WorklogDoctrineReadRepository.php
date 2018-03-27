<?php

namespace Kasperski\WorklogBundle\Repository\Read;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use Kasperski\WorklogBundle\Entity\Worklog;
use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class WorklogDoctrineReadRepository
{
    const WORKLOG_LIMIT = 5;
    const WORKLOG_REPOSITORY_NAMESPACE = 'WorklogBundle:Worklog';

    /**
     * @var EntityManager $em
     */
    private $em;

    /**
     * @var TokenStorage $tokenStorage
     */
    private $tokenStorage;

    /**
     * @param EntityManager $em
     * @param TokenStorage $tokenStorage
     */
    public function __construct(EntityManager $em, TokenStorage $tokenStorage)
    {
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param $page
     * @param $startedAt
     * @param $stoppedAt
     * @return Paginator
     */
    public function getForCurrentUser($page, $startedAt, $stoppedAt)
    {
        $queryBuilder = $this->em->createQueryBuilder();

        $queryBuilder
            ->select('w')
            ->from(self::WORKLOG_REPOSITORY_NAMESPACE, 'w')
            ->join('w.user', 'u')
            ->where('u.id = :user_id')
            ->orderBy('w.startedAt', 'desc')
            ->setParameter('user_id', $this->getUserId());

        if ($startedAt) {
            $queryBuilder
                ->andWhere('w.startedAt >= :started_at')
                ->setParameter('started_at', $startedAt);
        }

        if ($stoppedAt) {
            $queryBuilder
                ->andWhere('w.stoppedAt <= :stopped_at')
                ->setParameter('stopped_at', $stoppedAt);
        }

        return $this->paginate($queryBuilder->getQuery(), $page);
    }

    /**
     * @param string|null $startedAt
     * @param string|null $stoppedAt
     * @return array
     */
    public function getForCurrentUserExport($startedAt, $stoppedAt)
    {
        $queryBuilder = $this->em->createQueryBuilder();

        $queryBuilder
            ->select('w.id, u.username, w.startedAt, w.stoppedAt, w.timeSpentInSeconds, w.comment')
            ->from(self::WORKLOG_REPOSITORY_NAMESPACE, 'w')
            ->join('w.user', 'u')
            ->where('u.id = :user_id')
            ->orderBy('w.startedAt', 'desc')
            ->setParameter('user_id', $this->getUserId());

        if ($startedAt) {
            $queryBuilder
                ->andWhere('w.startedAt >= :started_at')
                ->setParameter('started_at', $startedAt);
        }

        if ($stoppedAt) {
            $queryBuilder
                ->andWhere('w.stoppedAt <= :stopped_at')
                ->setParameter('stopped_at', $stoppedAt);
        }

        return $queryBuilder->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);
    }

    /**
     * @param $dql
     * @param $page
     * @return Paginator
     */
    private function paginate($dql, $page)
    {
        $paginator = new Paginator($dql);

        $paginator->getQuery()
            ->setFirstResult(self::WORKLOG_LIMIT * ($page - 1))
            ->setMaxResults(self::WORKLOG_LIMIT);

        return $paginator;
    }

    /**
     * @return integer
     */
    private function getUserId()
    {
        return $this->tokenStorage->getToken()->getUser()->getId();
    }
}