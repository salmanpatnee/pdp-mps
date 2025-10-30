<?php

namespace App\Permissions;

use App\Models\User;

final class Abilities
{

    public const CreateJournal = 'journal:create';
    public const UpdateJournal = 'journal:update';
    public const DeleteJournal = 'journal:delete';

    // public const CreateOwnTicket = 'ticket:own:create';
    // public const UpdateOwnTicket = 'ticket:own:update';
    // public const DeleteOwnTicket = 'ticket:own:delete';

    public const CreateUser = 'user:create';
    public const UpdateUser = 'user:update';
    public const DeleteUser = 'user:delete';

    public static function getAbilities(User $user): array
    {
        if ($user->role_id === 1) {
            return [
                self::CreateJournal,
                self::UpdateJournal,
                self::DeleteJournal,
                self::CreateUser,
                self::UpdateUser,
                self::DeleteUser
            ];
        } else {
            return [];
        }
    }
}
