<?php

namespace Kasperski\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Kasperski\WorklogBundle\Entity\Worklog;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="\Kasperski\WorklogBundle\Entity\Worklog", mappedBy="user")
     */
    private $worklogs;

    public function __construct()
    {
        parent::__construct();
        $this->worklogs = new ArrayCollection();
    }

    /**
     * @return Worklog[]
     */
    public function getWorklogs()
    {
        return $this->worklogs;
    }

    public function addWorklog(Worklog $worklog)
    {
        $this->worklogs[] = $worklog;
    }
}
