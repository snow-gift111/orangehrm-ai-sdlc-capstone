<?php

declare(strict_types=1);

namespace App\LeaveAlert\Application\Service;

use App\LeaveAlert\Domain\Entity\InAppNotification;
use App\LeaveAlert\Domain\Entity\LeaveBalanceAlert;
use App\LeaveAlert\Domain\Service\AlertMessageBuilder;
use App\LeaveAlert\Infrastructure\Repository\InAppNotificationRepository;
use App\Security\Domain\Entity\User;
use Psr\Log\LoggerInterface;

/**
 * In-application notification handling.
 *
 * Requirements: LBA-FR-023, LBA-FR-024, LBA-FR-026, LBA-FR-027.
 *
 * Email and third-party channels are explicitly out of scope for this sprint.
 */
final class NotificationService
{
    public function __construct(
        private readonly InAppNotificationRepository $notificationRepository,
        private readonly RecipientResolutionService $recipientResolutionService,
        private readonly LeaveAlertAuthorizationService $authorizationService,
        private readonly AlertMessageBuilder $messageBuilder,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * Creates in-app notifications for every configured recipient of the rule
     * that generated the alert.
     *
     * Persistence is performed within the caller's transaction; the caller is
     * responsible for flushing.
     *
     * @return list<InAppNotification>
     */
    public function createNotificationsForAlert(LeaveBalanceAlert $alert): array
    {
        $recipients = $this->recipientResolutionService->resolve(
            $alert->getRule()->getRecipientTypes(),
            $alert->getEmployee()
        );

        $title = $this->messageBuilder->buildTitle($alert);
        $message = $this->messageBuilder->buildMessage($alert);

        $notifications = [];

        foreach ($recipients as $recipient) {
            $notification = new InAppNotification($recipient, $alert, $title, $message);
            $this->notificationRepository->save($notification);
            $notifications[] = $notification;
        }

        $this->logger->info('In-app leave balance alert notifications created.', [
            'rule_id' => $alert->getRule()->getId(),
            'recipient_count' => count($notifications),
        ]);

        return $notifications;
    }

    /**
     * Retrieves notifications belonging to the current user only. Alert details
     * are included only when the user remains authorized to view the related
     * alert at read time.
     *
     * @return list<InAppNotification>
     */
    public function listNotificationsForCurrentUser(User $currentUser, int $limit = 100): array
    {
        $notifications = $this->notificationRepository->findByRecipient($currentUser, $limit);

        return array_values(array_filter(
            $notifications,
            function (InAppNotification $notification) use ($currentUser): bool {
                $employee = $notification->getAlert()->getEmployee();

                if ($this->authorizationService->hasHrVisibility($currentUser)) {
                    return true;
                }

                return $this->authorizationService->isSelf($currentUser, $employee);
            }
        ));
    }
}
