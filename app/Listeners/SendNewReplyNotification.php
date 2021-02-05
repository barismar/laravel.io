<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\ReplyWasCreated;
use App\Models\User;
use App\Notifications\NewReplyNotification;

final class SendNewReplyNotification
{
    public function handle(ReplyWasCreated $event): void
    {
        foreach ($event->reply->replyAble()->subscriptions() as $subscription) {
            if ($this->replyAuthorDoesNotMatchSubscriber($event->reply->author(), $subscription)) {
                $subscription->user()->notify(new NewReplyNotification($event->reply, $subscription));
            }
        }
    }

    private function replyAuthorDoesNotMatchSubscriber(User $author, $subscription): bool
    {
        return ! $author->matches($subscription->user());
    }
}
