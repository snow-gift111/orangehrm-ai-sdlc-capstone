<?php

declare(strict_types=1);

namespace App\LeaveAlert\Domain\Entity;

use App\Security\Domain\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * In-application notification for a generated leave balance alert.
 *
 * Requirements: LBA-FR-023, LBA-FR-024, LBA-FR-026.
 * Read/unread state is explicitly excluded from the approved sprint scope.
 */
#[ORM\Entity(repositoryClass: \App\LeaveAlert\Infrastructure\Repository\InAppNotificationRepository::class)]
#[ORM\Table(name: 'in_app_notification')]
#[ORM\UniqueConstraint(name: 'uq_notification_recipient_alert', columns: ['recipient_user_id', 'alert_id'])]
#[ORM\Index(name: 'idx_notification_recipient_created', columns: ['recipient_user_id', 'created_at'])]
#[ORM\Index(name: 'idx_notification_alert', columns: ['alert_id'])]
class InAppNotification
{
    public const TYPE_LEAVE_BALANCE_ALERT = 'LEAVE_BALANCE_ALERT';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'recipient_user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private User $recipient;

    #[ORM\ManyToOne(targetEntity: LeaveBalanceAlert::class)]
    #[ORM\JoinColumn(name: 'alert_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private LeaveBalanceAlert $alert;

    #[ORM\Column(name: 'notification_type', type: 'string', length: 50)]
    private string $notificationType = self::TYPE_LEAVE_BALANCE_ALERT;

    #[ORM\Column(name: 'title', type: 'string', length: 200)]
    private string $title;

    #[ORM\Column(name: 'message', type: 'text')]
    private string $message;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct(
        User $recipient,
        LeaveBalanceAlert $alert,
        string $title,
        string $message,
        ?\DateTimeImmutable $now = null,
    ) {
        $this->recipient = $recipient;
        $this->alert = $alert;
        $this->notificationType = self::TYPE_LEAVE_BALANCE_ALERT;
        $this->title = $title;
        $this->message = $message;
        $this->createdAt = $now ?? new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRecipient(): User
    {
        return $this->recipient;
    }

    public function getAlert(): LeaveBalanceAlert
    {
        return $this->alert;
    }

    public function getNotificationType(): string
    {
        return $this->notificationType;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
