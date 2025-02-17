<?php

namespace Modules\User\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Modules\Core\Repositories\BaseRepository;
use Modules\User\Entities\UserToken;

interface UserTokenRepository extends BaseRepository
{
    /**
     * Get all tokens for the given user
     * @param int $userId
     * @return Collection
     */
    public function allForUser($userId);

    /**
     * @param int $userId
     * @return UserToken
     */
    public function generateFor($userId);


    public function refreshFor($token);
}
