<?php

namespace Cron\CronBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CronReport
 *
 * @ORM\Table(name="cron_report")
 * @ORM\Entity(repositoryClass="Cron\CronBundle\Repository\CronReportRepository")
 */
class CronReport
{
    /**
     * @var integer|null
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(name="run_at", type="datetime")
     * @var \DateTime|null
     */
    protected ?\DateTime $runAt = null;
    /**
     * @ORM\Column(name="run_time", type="float")
     * @var float|null
     */
    protected ?float $runTime = null;

    /**
     * @ORM\Column(name="exit_code", type="integer")
     * @var integer|null
     */
    protected ?int $exitCode = null;
    /**
     * @ORM\Column(type="text")
     * @var string|null
     */
    protected ?string $output = null;

    /**
     * @ORM\Column(type="text")
     * @var string|null
     */
    protected ?string $error = null;

    /**
     * @ORM\ManyToOne(targetEntity="CronJob", inversedBy="reports")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @var CronJob|null
     */
    protected ?CronJob $job = null;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param CronJob|null $job
     * @return CronReport
     */
    public function setJob(?CronJob $job): static
    {
        $this->job = $job;

        return $this;
    }

    /**
     * @return CronJob|null
     */
    public function getJob(): ?CronJob
    {
        return $this->job;
    }

    /**
     * @param string|null $output
     * @return CronReport
     */
    public function setOutput(?string $output): static
    {
        $this->output = $output;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getOutput(): ?string
    {
        return $this->output;
    }

    /**
     * @param string|null $error
     * @return CronReport
     */
    public function setError(?string $error): static
    {
        $this->error = $error;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getError(): ?string
    {
        return $this->error;
    }

    /**
     * @param int|null $exitCode
     * @return CronReport
     */
    public function setExitCode(?int $exitCode): static
    {
        $this->exitCode = $exitCode;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getExitCode(): ?int
    {
        return $this->exitCode;
    }

    /**
     * @param \DateTime|null $runAt
     * @return CronReport
     */
    public function setRunAt(?\DateTime $runAt): static
    {
        $this->runAt = $runAt;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getRunAt(): ?\DateTime
    {
        return $this->runAt;
    }

    /**
     * @param float|null $runTime
     * @return CronReport
     */
    public function setRunTime(?float $runTime): static
    {
        $this->runTime = $runTime;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getRunTime(): ?float
    {
        return $this->runTime;
    }
}
