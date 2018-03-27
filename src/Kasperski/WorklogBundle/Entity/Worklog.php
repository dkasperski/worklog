<?php

namespace Kasperski\WorklogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kasperski\WorklogBundle\Exceptions\WorklogNotStartedException;
use Symfony\Component\Validator\Constraints as Assert;
use Kasperski\UserBundle\Entity\User;

/**
 * @ORM\Entity
 * @ORM\Table(name="worklog")
 */
class Worklog
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="\Kasperski\UserBundle\Entity\User", inversedBy="worklogs")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="datetime", name="started_at", nullable=false)
     */
    private $startedAt;

    /**
     * @ORM\Column(type="datetime", name="paused_at", nullable=true)
     */
    private $pausedAt;

    /**
     * @ORM\Column(type="datetime", name="resumed_at", nullable=true)
     */
    private $resumedAt;

    /**
     * @ORM\Column(type="datetime", name="stopped_at", nullable=true)
     */
    private $stoppedAt;

    /**
     * @ORM\Column(name="time_spent_in_seconds", type="integer", nullable=false, options={"default":0})
     */
    private $timeSpentInSeconds;

    /**
     * @ORM\Column(type="text", length=1000, name="comment", nullable=true)
     */
    private $comment;

    /**
     * @param User $user
     * @param \DateTimeImmutable $startedAt
     * @param \DateTimeImmutable|null $pausedAt
     * @param \DateTimeImmutable|null $resumedAt
     * @param \DateTimeImmutable|null $stoppedAt
     * @param int $timeSpentInSeconds
     * @param string|null $comment
     */
    public function __construct(
        User $user,
        \DateTimeImmutable $startedAt,
        \DateTimeImmutable $pausedAt = null,
        \DateTimeImmutable $resumedAt = null,
        \DateTimeImmutable $stoppedAt = null,
        $timeSpentInSeconds = 0,
        $comment = null
    ) {
        $this->user = $user;
        $this->startedAt = $startedAt;
        $this->pausedAt = $pausedAt;
        $this->resumedAt = $resumedAt;
        $this->stoppedAt = $stoppedAt;
        $this->timeSpentInSeconds = $timeSpentInSeconds;
        $this->comment = $comment;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getStartedAt()
    {
        return $this->startedAt;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getPausedAt()
    {
        return $this->pausedAt;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getStoppedAt()
    {
        return $this->stoppedAt;
    }

    /**
     * @return integer
     */
    public function getTimeSpentInSeconds()
    {
        return $this->timeSpentInSeconds;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getResumedAt()
    {
        return $this->resumedAt;
    }

    /**
     * @param \DateTimeImmutable $dateTime
     * @return $this
     */
    public function setPausedAt(\DateTimeImmutable $dateTime)
    {
        $this->pausedAt = $dateTime;
        return $this;
    }

    /**
     * @param \DateTimeImmutable $dateTime
     * @return $this
     */
    public function setResumedAt(\DateTimeImmutable $dateTime)
    {
        $this->resumedAt = $dateTime;
        return $this;
    }

    /**
     * @param \DateTimeImmutable $dateTime
     * @return $this
     */
    public function setStoppedAt(\DateTimeImmutable $dateTime)
    {
        $this->stoppedAt = $dateTime;
        return $this;
    }

    /**
     * @param string $comment
     * @return $this
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * @param User $user
     * @return $this
     */
    public function setUser(User $user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @param $seconds
     * @return $this
     */
    private function setTimeSpentInSeconds($seconds)
    {
        $this->timeSpentInSeconds = $seconds;
        return $this;
    }
    
    

    public function measureTimeSpent()
    {
        if (!$this->startedAt) {
            throw new WorklogNotStartedException;
        }

        $secondsFromStartToFinishWorklog = $this->getSecondsFromStartToEndWorklog();
        $secondsDuringBreakWorklog = $this->getSecondsDuringBreakWorklog();
        $secondsSum = $secondsFromStartToFinishWorklog - $secondsDuringBreakWorklog;

        $this->setTimeSpentInSeconds($secondsSum);
    }

    /**
     * @return integer
     */
    private function getSecondsFromStartToEndWorklog()
    {
        return $this->stoppedAt->getTimestamp() -
        $this->startedAt->getTimestamp();
    }

    /**
     * @return integer
     */
    private function getSecondsDuringBreakWorklog()
    {
        $seconds = 0;

        if ($this->pausedAt && $this->resumedAt) {
            $seconds = $this->resumedAt->getTimestamp() -
                $this->pausedAt->getTimestamp();
        } elseif ($this->pausedAt && !$this->resumedAt) {
            
            $this->setResumedAt(new \DateTimeImmutable());
            $seconds = $this->resumedAt->getTimestamp() -
                $this->pausedAt->getTimestamp();
        }

        return $seconds;
    }
}
