<?php

namespace Kasperski\WorklogBundle\Repository\Write;

use Doctrine\ORM\EntityManager;
use Kasperski\WorklogBundle\Entity\Worklog;
use Symfony\Component\HttpFoundation\Session\Session;

class WorklogDoctrineWriteRepository
{
    /**
     * @var EntityManager $em
     */
    private $em;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param Worklog $worklog
     * @return bool
     */
    public function persist(Worklog $worklog)
    {
        try {
            $user = $this->em->find('UserBundle:User', $worklog->getUser()->getId());
            $worklog->setUser($user);
            
            $this->em->persist($worklog);
            $this->em->flush();
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }
}