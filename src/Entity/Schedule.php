<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ScheduleRepository")
 */
class Schedule
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var \DateTime $date
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @var Event $event
     * @ORM\ManyToOne(targetEntity="App\Entity\Event", inversedBy="schedules")
     */
    private $event;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(Event $event = null): self
    {
        if ($this->event) {
            $this->event->removeSchedule($this);
        }

        $this->event = $event;

        if ($this->event) {
            $event->addSchedule($this);
        }

        return $this;
    }
}
