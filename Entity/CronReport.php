<?php

namespace Cron\CronBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CronReport
 *
 * @ORM\Table(name="cron_report")
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
     * @ORM\Column(name="run_at", type="datetime")
     * @var \DateTime $runAt
     */
    protected $runAt;
    /**
     * @ORM\Column(name="run_time", type="float")
     * @var float $runTime
     */
    protected $runTime;

    /**
     * @ORM\Column(name="exit_code", type="integer")
     * @var integer $result
     */
    protected $exitCode;
    /**
     * @ORM\Column(type="text")
     * @var string $output
     */
    protected $output;

    /**
     * @ORM\Column(type="text")
     * @var string $error
     */
    protected $error;

    /**
     * @ORM\ManyToOne(targetEntity="CronJob", inversedBy="reports")
     * @ORM\JoinColumn(onDelete="CASCADE")
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
     * @return CronReport
     */
    public function setJob($job)
    {
        $this->job = $job;

        return $this;
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
     * @return CronReport
     */
    public function setOutput($output)
    {
        $this->output = $output;

        return $this;
    }

    /**
     * @return string
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @param string $error
     * @return CronReport
     */
    public function setError($error)
    {
        $this->error = $error;

        return $this;
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param int $exitCode
     * @return CronReport
     */
    public function setExitCode($exitCode)
    {
        $this->exitCode = $exitCode;

        return $this;
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
     * @return CronReport
     */
    public function setRunAt($runAt)
    {
        $this->runAt = $runAt;

        return $this;
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
     * @return CronReport
     */
    public function setRunTime($runTime)
    {
        $this->runTime = $runTime;

        return $this;
    }

    /**
     * @return float
     */
    public function getRunTime()
    {
        return $this->runTime;
    }
}
