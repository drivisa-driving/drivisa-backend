<?php

namespace Modules\User\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Collection;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use Modules\User\Entities\UserToken;
use Modules\User\Repositories\UserTokenRepository;
use Ramsey\Uuid\Uuid;

class EloquentUserTokenRepository extends EloquentBaseRepository implements UserTokenRepository
{
    /**
     * Get all tokens for the given user
     * @param int $userId
     * @return Collection
     */
    public function allForUser($userId)
    {
        return $this->model->where('user_id', $userId)->get();
    }

    /**
     * @param int $userId
     * @return UserToken
     */
    public function generateFor($userId)
    {
        $uuid4 = Uuid::uuid4();
        $data = [
            'user_id' => $userId,
            'access_token' => $uuid4,
            'agent' => request()->header('User-Agent'),
            'expired_at' => now()->addMinutes(config('ceo.user.config.ttl'))
        ];
        return $this->model->create($data);
    }

    public function refreshFor($token)
    {
        $uuid4 = Uuid::uuid4();
        $data = [
            'id' => $token->id,
            'user_id' => $token->user_id,
            'access_token' => $uuid4,
            'expired_at' => now()->addMinutes(config('ceo.user.config.ttl'))
        ];
        return $this->update($token, $data);
    }
}
