<?php

namespace Modules\User\Entities\Sentinel;

use Carbon\Carbon;
use Cartalyst\Sentinel\Laravel\Facades\Activation;
use Cartalyst\Sentinel\Users\EloquentUser;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laracasts\Presenter\PresentableTrait;
use Modules\Drivisa\Entities\Course;
use Modules\Drivisa\Entities\Complaint;
use Modules\Drivisa\Entities\ComplaintReply;
use Modules\Drivisa\Entities\Instructor;
use Modules\Drivisa\Entities\RentalRequest;
use Modules\Drivisa\Entities\Trainee;
use Modules\Drivisa\Services\StatsService;
use Modules\Media\Support\Traits\MediaRelation;
use Modules\User\Database\factories\UserFactory;
use Modules\User\Entities\ReferralCode;
use Modules\User\Entities\UserDevice;
use Modules\User\Entities\UserInterface;
use Modules\User\Entities\UserToken;
use Modules\User\Presenters\UserPresenter;

class User extends EloquentUser implements UserInterface, AuthenticatableContract
{
    use PresentableTrait, Authenticatable, SoftDeletes, MediaRelation, Notifiable, HasFactory;

    public const USER_TYPES = [
        'admin' => 0,
        'instructor' => 1,
        'trainee' => 2,
    ];
    protected $appends = ['full_name', 'credit', 'player_id', 'total_credit', 'pending_road_test_payment_count', 'latest_road_test_id'];
    protected $fillable = [
        'email',
        'password',
        'permissions',
        'first_name',
        'last_name',
        'address',
        'phone_number',
        'city',
        'postal_code',
        'province',
        'street',
        'unit_no',
        'username',
        'user_type',
        'created_by',
        'blocked_at',
        'deleted',
        'from_hear'
    ];
    /**
     * {@inheritDoc}
     */
    protected $loginNames = ['email'];
    protected $presenter = UserPresenter::class;

    protected $statsService;

    public function __construct(array $attributes = [])
    {
        $this->loginNames = config('ceo.user.config.login-columns');
        $this->fillable = config('ceo.user.config.fillable');
        if (config()->has('ceo.user.config.presenter')) {
            $this->presenter = config('ceo.user.config.presenter', UserPresenter::class);
        }
        if (config()->has('ceo.user.config.dates')) {
            $this->dates = config('ceo.user.config.dates', []);
        }
        if (config()->has('ceo.user.config.casts')) {
            $this->casts = config('ceo.user.config.casts', []);
        }

        $this->statsService = app(StatsService::class);

        parent::__construct($attributes);
    }

    /**
     * @inheritdoc
     */
    public function hasRoleId($roleId)
    {
        return $this->roles()->whereId($roleId)->count() >= 1;
    }

    /**
     * @inheritdoc
     */
    public function hasRoleSlug($slug)
    {
        return $this->roles()->whereSlug($slug)->count() >= 1;
    }

    /**
     * @inheritdoc
     */
    public function hasAnyRole(array $roles): bool
    {
        return $this->roles()->whereIn('slug', $roles)->count() >= 1;
    }

    /**
     * @inheritdoc
     */
    public function hasRoleName($name)
    {
        return $this->roles()->whereName($name)->count() >= 1;
    }

    /**
     * @inheritdoc
     */
    public function isActivated()
    {
        if (is_integer($this->getKey()) && Activation::completed($this)) {
            return true;
        }

        return false;
    }

    /**
     * @return HasMany
     */
    public function api_keys()
    {
        return $this->hasMany(UserToken::class);
    }

    /**
     * @inheritdoc
     */
    public function getFirstApiKey()
    {
        $userToken = $this->api_keys->last();
        return $userToken;
    }

    public function __call($method, $parameters)
    {
        #i: Convert array to dot notation
        $config = implode('.', ['ceo.user.config.relations', $method]);

        #i: Relation method resolver
        if (config()->has($config)) {
            $function = config()->get($config);
            $bound = $function->bindTo($this);

            return $bound();
        }

        #i: No relation found, return the call to parent (Eloquent) to handle it.
        return parent::__call($method, $parameters);
    }

    /**
     * @inheritdoc
     */
    public function hasAccess($permission)
    {
        $permissions = $this->getPermissionsInstance();

        return $permissions->hasAccess($permission);
    }

    public function isSuperAdmin()
    {
        return in_array($this->email, config('ceo.user.config.saEmails', ['info@drivisa.com']));
    }

    public function isVerified()
    {
        $isVerified = false;
        if ($this->user_type === User::USER_TYPES['instructor']) {
            $isVerified = $this->instructor ? $this->instructor->verified : false;
        } else {
            $isVerified = $this->trainee ? $this->trainee->verified : false;
        }
        return $isVerified;
    }

    public function trainee()
    {
        return $this->hasOne(Trainee::class);
    }

    public function instructor()
    {
        return $this->hasOne(Instructor::class);
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function complaint()
    {
        return $this->hasMany(Complaint::class);
    }

    public function complaintReply()
    {
        return $this->hasMany(ComplaintReply::class);
    }

    public function getCreditAttribute()
    {
        $packageStats = $this->statsService->getStatsByType($this, "Package", true);
        $remainingHours = $packageStats['data']['remaining_hours'] ?? 0;

        return $this->trainee ? $remainingHours : 0;
    }

    public function getTotalCreditAttribute()
    {
        $bdeStats = $this->statsService->getStatsByType($this, "BDE", true);
        $packageStats = $this->statsService->getStatsByType($this, "Package", true);

        $remainingHours = ($bdeStats['data']['remaining_hours'] ?? 0) + ($packageStats['data']['remaining_hours'] ?? 0);

        return $this->trainee ? $remainingHours : 0;
    }

    public function getPendingRoadTestPaymentCountAttribute()
    {
        if ($this->trainee) {
            // return $this->trainee->rentalRequests()
            //     ->where('status', RentalRequest::STATUS['accepted'])
            //     ->count();

            $isExpire = false;
            $diff = 0;
            $currentTime = Carbon::now();
            $booking_datetime = $this->trainee->rentalRequests()
                ->where('status', RentalRequest::STATUS['accepted'])
                ->select('booking_date', 'booking_time', 'expire_payment_time')
                ->get();

            foreach ($booking_datetime as $datetime) {
                $date = $datetime->booking_date->toDateString();
                $time = $datetime->booking_time->toTimeString();
                $dateTime = Carbon::parse($date . $time);
                if ($dateTime->gt($currentTime)) {
                    if ($datetime->expire_payment_time->gt($currentTime)) {
                        $diff++;
                    }
                }
            }
            return $diff;
        }
    }

    public function getLatestRoadTestIdAttribute()
    {
        if ($this->trainee) {
            return $this->trainee->rentalRequests()
                ->where('status', RentalRequest::STATUS['accepted'])
                ->select('id')
                ->latest()
                ->first()?->id;
        }
    }

    public static function newFactory()
    {
        return UserFactory::new();
    }

    public function userDevices()
    {
        return $this->hasMany(UserDevice::class);
    }

    public function referralCodes()
    {
        return $this->hasMany(ReferralCode::class);
    }

    public function activeReferralCode()
    {
        return $this->hasOne(ReferralCode::class)->whereNull('sent_at');
    }

    public function getPlayerIdAttribute()
    {
        return $this->userDevices->pluck('player_id')->toArray();
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . " " . $this->last_name;
    }
}
