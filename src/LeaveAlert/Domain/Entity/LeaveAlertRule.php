<?php

declare(strict_types=1);

namespace App\LeaveAlert\Domain\Entity;

use App\LeaveAlert\Domain\Enum\RecipientType;
use App\LeaveAlert\Domain\Enum\TriggerCondition;
use App\Security\Domain\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Configurable leave balance alert rule.
 *
 * Requirements: LBA-FR-006 .. LBA-FR-013.
 * Only active rules participate in alert evaluation (LBA-FR-010).
 */
#[ORM\Entity(repositoryClass: \App\LeaveAlert\Infrastructure\Repository\LeaveAlertRuleRepository::class)]
#[ORM\Table(name: 'leave_alert_rule')]
#[ORM\Index(name: 'idx_alert_rule_active', columns: ['is_active'])]
#[ORM\Index(name: 'idx_alert_rule_type_active', columns: ['leave_type_code', 'is_active'])]
class LeaveAlertRule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'rule_name', type: 'string', length: 150)]
    private string $ruleName;

    #[ORM\Column(name: 'threshold_value', type: 'decimal', precision: 10, scale: 2)]
    private string $thresholdValue;

    #[ORM\Column(name: 'trigger_condition', type: 'string', length: 30, enumType: TriggerCondition::class)]
    private TriggerCondition $triggerCondition;

    #[ORM\Column(name: 'leave_type_code', type: 'string', length: 50, nullable: true)]
    private ?string $leaveTypeCode = null;

    #[ORM\Column(name: 'is_active', type: 'boolean')]
    private bool $active = true;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'created_by', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?User $createdBy = null;

    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'updated_by', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?User $updatedBy = null;

    /**
     * @var Collection<int, LeaveAlertRuleRecipient>
     */
    #[ORM\OneToMany(
        mappedBy: 'rule',
        targetEntity: LeaveAlertRuleRecipient::class,
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    private Collection $recipients;

    public function __construct(
        string $ruleName,
        string $thresholdValue,
        TriggerCondition $triggerCondition,
        ?string $leaveTypeCode,
        bool $active,
        ?User $actor,
        ?\DateTimeImmutable $now = null,
    ) {
        $timestamp = $now ?? new \DateTimeImmutable();

        $this->ruleName = $ruleName;
        $this->thresholdValue = $thresholdValue;
        $this->triggerCondition = $triggerCondition;
        $this->leaveTypeCode = $leaveTypeCode;
        $this->active = $active;
        $this->createdBy = $actor;
        $this->updatedBy = $actor;
        $this->createdAt = $timestamp;
        $this->updatedAt = $timestamp;
        $this->recipients = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRuleName(): string
    {
        return $this->ruleName;
    }

    public function getThresholdValue(): string
    {
        return $this->thresholdValue;
    }

    public function getThresholdValueAsFloat(): float
    {
        return (float) $this->thresholdValue;
    }

    public function getTriggerCondition(): TriggerCondition
    {
        return $this->triggerCondition;
    }

    public function getLeaveTypeCode(): ?string
    {
        return $this->leaveTypeCode;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getUpdatedBy(): ?User
    {
        return $this->updatedBy;
    }

    /**
     * @return Collection<int, LeaveAlertRuleRecipient>
     */
    public function getRecipients(): Collection
    {
        return $this->recipients;
    }

    /**
     * @return list<RecipientType>
     */
    public function getRecipientTypes(): array
    {
        return array_values(
            array_map(
                static fn (LeaveAlertRuleRecipient $recipient): RecipientType => $recipient->getRecipientType(),
                $this->recipients->toArray()
            )
        );
    }

    public function addRecipientType(RecipientType $recipientType, ?\DateTimeImmutable $now = null): void
    {
        if (in_array($recipientType, $this->getRecipientTypes(), true)) {
            return;
        }

        $this->recipients->add(new LeaveAlertRuleRecipient($this, $recipientType, $now));
    }

    /**
     * Replaces the configured recipient set with the supplied recipient types.
     *
     * @param list<RecipientType> $recipientTypes
     */
    public function replaceRecipientTypes(array $recipientTypes, ?\DateTimeImmutable $now = null): void
    {
        $this->recipients->clear();

        foreach ($recipientTypes as $recipientType) {
            $this->addRecipientType($recipientType, $now);
        }
    }

    /**
     * Applies edited rule attributes (LBA-FR-011).
     */
    public function applyChanges(
        string $ruleName,
        string $thresholdValue,
        TriggerCondition $triggerCondition,
        ?string $leaveTypeCode,
        ?User $actor,
        ?\DateTimeImmutable $now = null,
    ): void {
        $this->ruleName = $ruleName;
        $this->thresholdValue = $thresholdValue;
        $this->triggerCondition = $triggerCondition;
        $this->leaveTypeCode = $leaveTypeCode;
        $this->touch($actor, $now);
    }

    public function activate(?User $actor, ?\DateTimeImmutable $now = null): void
    {
        $this->active = true;
        $this->touch($actor, $now);
    }

    public function deactivate(?User $actor, ?\DateTimeImmutable $now = null): void
    {
        $this->active = false;
        $this->touch($actor, $now);
    }

    private function touch(?User $actor, ?\DateTimeImmutable $now): void
    {
        $this->updatedBy = $actor;
        $this->updatedAt = $now ?? new \DateTimeImmutable();
    }
}
