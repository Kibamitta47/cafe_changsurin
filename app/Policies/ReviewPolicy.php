<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReviewPolicy
{
    use HandlesAuthorization;

    // ตรวจสอบว่าผู้ใช้สามารถอัปเดตรีวิวได้หรือไม่
    public function update(User $user, Review $review): bool
    {
        return $user->id === $review->user_id;
    }

    // ตรวจสอบว่าผู้ใช้สามารถลบรีวิวได้หรือไม่
    public function delete(User $user, Review $review): bool
    {
        return $user->id === $review->user_id;
    }
}