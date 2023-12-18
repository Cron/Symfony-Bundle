<?php

namespace Cron\CronBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * CronJob
 *
 * @ORM\Table(name="cron_job", uniqueConstraints={@ORM\UniqueConstraint(name="un_name", columns={"name"})})
 * @ORM\Entity(repositoryClass="Cron\CronBundle\Repository\CronJobRepository")
 */
class CronJob
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
     * @var string|null
     *
     * @ORM\Column(name="name", type="string", length=191)
     */
    private ?string $name = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="command", type="string", length=1024)
     */
    private ?string $command = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="schedule", type="string", length=191)
     */
    private ?string $schedule = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="string", length=191)
     */
    private ?string $description = null;

    /**
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    private ?bool $enabled = null;

    /**
     * @ORM\OneToMany(targetEntity="CronReport", mappedBy="job", cascade={"remove"})
     * @var ArrayCollection
     */
    protected Collection $reports;

    public function __construct()
    {
        $this->reports = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string|null $name
     * @return CronJob
     */
    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $command
     * @return CronJob
     */
    public function setCommand(?string $command): static
    {
        $this->command = $command;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCommand(): ?string
    {
        return $this->command;
    }

    /**
     * @param string|null $schedule
     * @return CronJob
     */
    public function setSchedule(?string $schedule): static
    {
        $this->schedule = $schedule;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSchedule(): ?string
    {
        return $this->schedule;
    }

    /**
     * Set description
     *
     * @param string|null $description
     * @return CronJob
     */
    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set enabled
     *
     * @param boolean|null $enabled
     * @return CronJob
     */
    public function setEnabled(?bool $enabled): static
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean|null
     */
    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    /**
     * @return Collection
     */
    public function getReports(): Collection
    {
        return $this->reports;
    }

    public function __toString()
    {
        return $this->name;
    }
}
