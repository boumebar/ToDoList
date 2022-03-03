<?php

namespace App\Security\Voter;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class TaskVoter extends Voter
{
    public const EDIT = 'task_edit';
    public const DELETE = 'task_delete';

    protected function supports(string $attribute, $task): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::DELETE])
            && $task instanceof \App\Entity\Task;
    }

    protected function voteOnAttribute(string $attribute, $task, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        if ($task->getAuthor()  === null) return false;

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($task, $user);
                // logic to determine if the user can EDIT
                // return true or false
                break;
            case self::DELETE:
                return $this->canDelete($task, $user);
                // logic to determine if the user can VIEW
                // return true or false
                break;
        }

        return false;
    }

    private function canEdit(Task $task, User $user): bool
    {
        // this assumes that the Post object has a `getOwner()` method
        return $user === $task->getAuthor();
    }

    private function canDelete(Task $task, User $user): bool
    {
        // this assumes that the Post object has a `getOwner()` method
        return $user === $task->getAuthor();
    }
}
