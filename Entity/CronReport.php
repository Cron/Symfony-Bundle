<?php

namespace Cron\CronBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CronReport
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Cron\CronBundle\Entity\CronReportRepository")
 */
class CronReport
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime $runAt
     */
    protected $runAt;
    /**
     * @ORM\Column(type="float")
     * @var float $runTime
     */
    protected $runTime;

    /**
     * @ORM\Column(type="integer")
     * @var integer $result
     */
    protected $exitCode;
    /**
     * @ORM\Column(type="text")
     * @var string $output
     */
    protected $output;

    /**
     * @ORM\ManyToOne(targetEntity="CronJob", inversedBy="reports")
     * @var CronJob
     */
    protected $job;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param CronJob $job
     */
    public function setJob($job)
    {
        $this->job = $job;
    }

    /**
     * @return CronJob
     */
    public function getJob()
    {
        return $this->job;
    }

    /**
     * @param string $output
     */
    public function setOutput($output)
    {
        $this->output = $output;
    }

    /**
     * @return string
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @param int $exitCode
     */
    public function setExitCode($exitCode)
    {
        $this->exitCode = $exitCode;
    }

    /**
     * @return int
     */
    public function getExitCode()
    {
        return $this->exitCode;
    }

    /**
     * @param \DateTime $runAt
     */
    public function setRunAt($runAt)
    {
        $this->runAt = $runAt;
    }

    /**
     * @return \DateTime
     */
    public function getRunAt()
    {
        return $this->runAt;
    }

    /**
     * @param float $runTime
     */
    public function setRunTime($runTime)
    {
        $this->runTime = $runTime;
    }

    /**
     * @return float
     */
    public function getRunTime()
    {
        return $this->runTime;
    }
}
