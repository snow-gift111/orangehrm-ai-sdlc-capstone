<?php

declare(strict_types=1);

namespace App\LeaveAlert\Domain\Entity;

use App\LeaveAlert\Domain\Enum\RecipientType;
use Doctrine\ORM\Mapping as ORM;

/**
 * Recipient configuration for a leave balance alert rule (LBA-FR-009).
 *
 * At least one recipient must exist per saved rule; the invariant is enforced
 * by the application service layer before persistence.
 */
#[ORM\Entity(repositoryClass: \App\LeaveAlert\Infrastructure\Repository\LeaveAlertRuleRecipientRepository::class)]
#[ORM\Table(name: 'leave_alert_rule_recipient')]
#[ORM\UniqueConstraint(name: 'uq_rule_recipient', columns: ['rule_id', 'recipient_type'])]
#[ORM\Index(name: 'idx_rule_recipient_rule', columns: ['rule_id'])]
class LeaveAlertRuleRecipient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: LeaveAlertRule::class, inversedBy: 'recipients')]
    #[ORM\JoinColumn(name: 'rule_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private LeaveAlertRule $rule;

    #[ORM\Column(name: 'recipient_type', type: 'string', length: 30, enumType: RecipientType::class)]
    private RecipientType $recipientType;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct(LeaveAlertRule $rule, RecipientType $recipientType, ?\DateTimeImmutable $now = null)
    {
        $this->rule = $rule;
        $this->recipientType = $recipientType;
        $this->createdAt = $now ?? new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRule(): LeaveAlertRule
    {
        return $this->rule;
    }

    public function getRecipientType(): RecipientType
    {
        return $this->recipientType;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
