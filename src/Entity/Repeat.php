<?php

namespace App\Entity;

use App\Repository\RepeatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RepeatRepository::class)
 * @ORM\Table(name="`repeat`")
 */
class Repeat
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $repetation;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $repeatUntil;

    /**
     * @ORM\OneToMany(targetEntity=Rooms::class, mappedBy="repeater")
     */
    private $rooms;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="repeaterUsers")
     */
    private $participants;

    /**
     * @ORM\Column(type="array")
     */
    private $weekday = [];

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $weeks;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $months;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $days;

    /**
     * @ORM\Column(type="integer")
     */
    private $repeatType;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $repeaterDays;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $repeaterWeeks;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $RepeatMontly;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $RepeatYearly;

    /**
     * @ORM\Column(type="datetime")
     */
    private $startDate;

    /**
     * @ORM\OneToOne(targetEntity=Rooms::class, inversedBy="repeaterProtoype", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $prototyp;



    public function __construct()
    {
        $this->rooms = new ArrayCollection();
        $this->participants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRepetation(): ?int
    {
        return $this->repetation;
    }

    public function setRepetation(?int $repetation): self
    {
        $this->repetation = $repetation;

        return $this;
    }

    public function getRepeatUntil(): ?\DateTimeInterface
    {
        return $this->repeatUntil;
    }

    public function setRepeatUntil(?\DateTimeInterface $repeatUntil): self
    {
        $this->repeatUntil = $repeatUntil;

        return $this;
    }

    /**
     * @return Collection|Rooms[]
     */
    public function getRooms(): Collection
    {
        return $this->rooms;
    }

    public function addRoom(Rooms $room): self
    {
        if (!$this->rooms->contains($room)) {
            $this->rooms[] = $room;
            $room->setRepeater($this);
        }

        return $this;
    }

    public function removeRoom(Rooms $room): self
    {
        if ($this->rooms->removeElement($room)) {
            // set the owning side to null (unless already changed)
            if ($room->getRepeater() === $this) {
                $room->setRepeater(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(User $participant): self
    {
        if (!$this->participants->contains($participant)) {
            $this->participants[] = $participant;
        }

        return $this;
    }

    public function removeParticipant(User $participant): self
    {
        $this->participants->removeElement($participant);

        return $this;
    }

    public function getWeekday(): ?array
    {
        return $this->weekday;
    }

    public function setWeekday(array $weekday): self
    {
        $this->weekday = $weekday;

        return $this;
    }

    public function getWeeks(): ?int
    {
        return $this->weeks;
    }

    public function setWeeks(?int $weeks): self
    {
        $this->weeks = $weeks;

        return $this;
    }

    public function getMonths(): ?int
    {
        return $this->months;
    }

    public function setMonths(?int $months): self
    {
        $this->months = $months;

        return $this;
    }

    public function getDays(): ?int
    {
        return $this->days;
    }

    public function setDays(?int $days): self
    {
        $this->days = $days;

        return $this;
    }

    public function getRepeatType(): ?int
    {
        return $this->repeatType;
    }

    public function setRepeatType(int $repeatType): self
    {
        $this->repeatType = $repeatType;

        return $this;
    }

    public function getRepeaterDays(): ?int
    {
        return $this->repeaterDays;
    }

    public function setRepeaterDays(?int $repeaterDays): self
    {
        $this->repeaterDays = $repeaterDays;

        return $this;
    }

    public function getRepeaterWeeks(): ?int
    {
        return $this->repeaterWeeks;
    }

    public function setRepeaterWeeks(?int $repeaterWeeks): self
    {
        $this->repeaterWeeks = $repeaterWeeks;

        return $this;
    }

    public function getRepeatMontly(): ?int
    {
        return $this->RepeatMontly;
    }

    public function setRepeatMontly(?int $RepeatMontly): self
    {
        $this->RepeatMontly = $RepeatMontly;

        return $this;
    }

    public function getRepeatYearly(): ?int
    {
        return $this->RepeatYearly;
    }

    public function setRepeatYearly(?int $RepeatYearly): self
    {
        $this->RepeatYearly = $RepeatYearly;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getPrototyp(): ?Rooms
    {
        return $this->prototyp;
    }

    public function setPrototyp(Rooms $prototyp): self
    {
        $this->prototyp = $prototyp;

        return $this;
    }
}