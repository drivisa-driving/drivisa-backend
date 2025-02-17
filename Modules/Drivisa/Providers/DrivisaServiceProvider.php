<?php

namespace Modules\Drivisa\Providers;

use Illuminate\Support\Arr;
use Illuminate\Routing\Router;
use Modules\Drivisa\Entities\Car;
use Modules\Drivisa\Entities\Admin;
use Modules\Drivisa\Entities\Point;
use Modules\Drivisa\Entities\BDELog;
use Modules\Drivisa\Entities\Course;
use Modules\Drivisa\Entities\Lesson;
use Modules\Drivisa\Entities\Trainee;
use Modules\Drivisa\Entities\Purchase;
use Illuminate\Support\ServiceProvider;
use Modules\Drivisa\Entities\Complaint;
use Modules\Drivisa\Entities\Instructor;
use Modules\Drivisa\Entities\MarkingKey;
use Modules\Drivisa\Entities\WorkingDay;
use Modules\User\Entities\Sentinel\User;
use Modules\Drivisa\Entities\Transaction;
use Modules\Drivisa\Entities\WorkingHour;
use Modules\Drivisa\Entities\FinalTestKey;
use Modules\Drivisa\Entities\FinalTestLog;
use Modules\Drivisa\Entities\MarkingKeyLog;
use Modules\Drivisa\Entities\RentalRequest;
use Modules\Drivisa\Entities\SavedLocation;
use Modules\Drivisa\Entities\StripeAccount;
use Modules\Drivisa\Entities\ComplaintReply;
use Modules\Drivisa\Entities\FinalTestResult;
use Modules\Drivisa\Entities\TrainingLocation;
use Modules\Drivisa\Entities\StripeBankAccount;
use Modules\Drivisa\Repositories\CarRepository;
use Modules\Core\Traits\CanPublishConfiguration;
use Modules\Drivisa\Entities\LessonCancellation;
use Modules\Drivisa\Entities\EvaluationIndicator;
use Modules\Drivisa\Repositories\PointRepository;
use Modules\Drivisa\Repositories\BDELogRepository;
use Modules\Drivisa\Repositories\CourseRepository;
use Modules\Drivisa\Repositories\LessonRepository;
use Modules\Core\Events\LoadingBackendTranslations;
use Modules\Drivisa\Console\CreateInstructorCommand;
use Modules\Drivisa\Repositories\PurchaseRepository;
use Modules\Drivisa\Repositories\ComplaintRepository;
use Modules\Drivisa\Http\Middleware\EnsureUserIsAdmin;
use Modules\Drivisa\Repositories\MarkingKeyRepository;
use Modules\Drivisa\Repositories\WorkingDayRepository;
use Modules\Drivisa\Repositories\TransactionRepository;
use Modules\Drivisa\Repositories\WorkingHourRepository;
use Modules\Drivisa\Repositories\FinalTestKeyRepository;
use Modules\Drivisa\Repositories\FinalTestLogRepository;
use Modules\Drivisa\Repositories\Cache\CacheCarDecorator;
use Modules\Drivisa\Repositories\MarkingKeyLogRepository;
use Modules\Drivisa\Repositories\RentalRequestRepository;
use Modules\Drivisa\Repositories\SavedLocationRepository;
use Modules\Drivisa\Repositories\StripeAccountRepository;
use Modules\Drivisa\Repositories\ComplaintReplyRepository;
use Modules\Drivisa\Http\Middleware\EnsureUserIsSuperAdmin;
use Modules\Drivisa\Repositories\Cache\CacheAdminDecorator;
use Modules\Drivisa\Repositories\Cache\CachePointDecorator;
use Modules\Drivisa\Repositories\FinalTestResultRepository;
use Modules\Drivisa\Repositories\Cache\CacheCourseDecorator;
use Modules\Drivisa\Repositories\Cache\CacheLessonDecorator;
use Modules\Drivisa\Repositories\TrainingLocationRepository;
use Modules\Drivisa\Repositories\Cache\CacheTraineeDecorator;
use Modules\Drivisa\Repositories\StripeBankAccountRepository;
use Modules\Drivisa\Repositories\LessonCancellationRepository;
use Modules\Drivisa\Http\Middleware\EnsureInstructorIsVerified;
use Modules\Drivisa\Repositories\EvaluationIndicatorRepository;
use Modules\Drivisa\Repositories\Cache\CacheInstructorDecorator;
use Modules\Drivisa\Repositories\Cache\CacheWorkingDayDecorator;
use Modules\Drivisa\Repositories\Eloquent\EloquentCarRepository;
use Modules\Drivisa\Repositories\Cache\CacheWorkingHourDecorator;
use Modules\Drivisa\Repositories\Eloquent\EloquentAdminRepository;
use Modules\Drivisa\Repositories\Eloquent\EloquentPointRepository;
use Modules\Drivisa\Repositories\Cache\CacheStripeAccountDecorator;
use Modules\Drivisa\Repositories\Eloquent\EloquentBDELogRepository;
use Modules\Drivisa\Repositories\Eloquent\EloquentCourseRepository;
use Modules\Drivisa\Repositories\Eloquent\EloquentLessonRepository;
use Modules\Drivisa\Repositories\Eloquent\EloquentTraineeRepository;
use Modules\Drivisa\Repositories\Eloquent\EloquentPurchaseRepository;
use Modules\Drivisa\Repositories\Eloquent\EloquentComplaintRepository;
use Modules\Drivisa\Repositories\Cache\CacheStripeBankAccountDecorator;
use Modules\Drivisa\Repositories\Eloquent\EloquentInstructorRepository;
use Modules\Drivisa\Repositories\Eloquent\EloquentMarkingKeyRepository;
use Modules\Drivisa\Repositories\Eloquent\EloquentWorkingDayRepository;
use Modules\Drivisa\Repositories\Eloquent\EloquentTransactionRepository;
use Modules\Drivisa\Repositories\Eloquent\EloquentWorkingHourRepository;
use Modules\Drivisa\Repositories\Cache\CacheEvaluationIndicatorDecorator;
use Modules\Drivisa\Repositories\Eloquent\EloquentFinalTestKeyRepository;
use Modules\Drivisa\Repositories\Eloquent\EloquentFinalTestLogRepository;
use Modules\Drivisa\Repositories\Eloquent\EloquentMarkingKeyLogRepository;
use Modules\Drivisa\Repositories\Eloquent\EloquentRentalRequestRepository;
use Modules\Drivisa\Repositories\Eloquent\EloquentSavedLocationRepository;
use Modules\Drivisa\Repositories\Eloquent\EloquentStripeAccountRepository;
use Modules\Drivisa\Repositories\Eloquent\EloquentComplaintReplyRepository;
use Modules\Drivisa\Repositories\Eloquent\EloquentFinalTestResultRepository;
use Modules\Drivisa\Repositories\Eloquent\EloquentTrainingLocationRepository;
use Modules\Drivisa\Repositories\Eloquent\EloquentStripeBankAccountRepository;
use Modules\Drivisa\Repositories\Eloquent\EloquentLessonCancellationRepository;
use Modules\Drivisa\Repositories\Eloquent\EloquentEvaluationIndicatorRepository;

class DrivisaServiceProvider extends ServiceProvider
{
    use CanPublishConfiguration;

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    protected $routeMiddleware = [
        'verified' => EnsureInstructorIsVerified::class,
        'is-admin' => EnsureUserIsAdmin::class,
        'is-super-admin' => EnsureUserIsSuperAdmin::class,
    ];

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerBindings();

        $this->app['events']->listen(LoadingBackendTranslations::class, function (LoadingBackendTranslations $event) {
            $event->load('core', Arr::dot(trans('drivisa::drivisa')));
        });

        $this->registerCommands();
    }

    private function registerBindings()
    {
        $this->app->bind(
            'Modules\Drivisa\Repositories\AdminRepository',
            function () {
                $repository = new EloquentAdminRepository(new Admin());

                if (!config('app.cache')) {
                    return $repository;
                }

                return new CacheAdminDecorator($repository);
            }
        );

        $this->app->bind(
            'Modules\Drivisa\Repositories\InstructorRepository',
            function () {
                $repository = new EloquentInstructorRepository(new Instructor());

                if (!config('app.cache')) {
                    return $repository;
                }

                return new CacheInstructorDecorator($repository);
            }
        );
        $this->app->bind(
            'Modules\Drivisa\Repositories\TraineeRepository',
            function () {
                $repository = new EloquentTraineeRepository(new Trainee());

                if (!config('app.cache')) {
                    return $repository;
                }

                return new CacheTraineeDecorator($repository);
            }
        );

        $this->app->bind(
            PointRepository::class,
            function () {
                $repository = new EloquentPointRepository(new Point());
                if (!config('app.cache')) {
                    return $repository;
                }

                return new CachePointDecorator($repository);
            }
        );
        $this->app->bind(
            WorkingHourRepository::class,
            function () {
                $repository = new EloquentWorkingHourRepository(new WorkingHour());

                if (!config('app.cache')) {
                    return $repository;
                }

                return new CacheWorkingHourDecorator($repository);
            }
        );
        $this->app->bind(
            WorkingDayRepository::class,
            function () {
                $repository = new EloquentWorkingDayRepository(new WorkingDay());

                if (!config('app.cache')) {
                    return $repository;
                }

                return new CacheWorkingDayDecorator($repository);
            }
        );
        $this->app->bind(
            LessonRepository::class,
            function () {
                $repository = new EloquentLessonRepository(new Lesson());

                if (!config('app.cache')) {
                    return $repository;
                }

                return new CacheLessonDecorator($repository);
            }
        );

        $this->app->bind(
            LessonCancellationRepository::class,
            function () {
                $repository = new EloquentLessonCancellationRepository(new LessonCancellation());

                return $repository;
            }
        );

        $this->app->bind(
            StripeAccountRepository::class,
            function () {
                $repository = new EloquentStripeAccountRepository(new StripeAccount());

                if (!config('app.cache')) {
                    return $repository;
                }

                return new CacheStripeAccountDecorator($repository);
            }
        );
        $this->app->bind(
            StripeBankAccountRepository::class,
            function () {
                $repository = new EloquentStripeBankAccountRepository(new StripeBankAccount());

                if (!config('app.cache')) {
                    return $repository;
                }
                return new CacheStripeBankAccountDecorator($repository);
            }
        );
        $this->app->bind(
            EvaluationIndicatorRepository::class,
            function () {
                $repository = new EloquentEvaluationIndicatorRepository(new EvaluationIndicator());

                if (!config('app.cache')) {
                    return $repository;
                }
                return new CacheEvaluationIndicatorDecorator($repository);
            }
        );
        $this->app->bind(
            CarRepository::class,
            function () {
                $repository = new EloquentCarRepository(new Car());
                if (!config('app.cache')) {
                    return $repository;
                }

                return new CacheCarDecorator($repository);
            }
        );
        $this->app->bind(
            ComplaintRepository::class,
            function () {
                return new EloquentComplaintRepository(new Complaint());
            }
        );
        $this->app->bind(
            ComplaintReplyRepository::class,
            function () {
                return new EloquentComplaintReplyRepository(new ComplaintReply());
            }
        );
        $this->app->bind(
            MarkingKeyRepository::class,
            function () {
                return new EloquentMarkingKeyRepository(new MarkingKey());
            }
        );
        $this->app->bind(
            FinalTestKeyRepository::class,
            function () {
                return new EloquentFinalTestKeyRepository(new FinalTestKey());
            }
        );
        $this->app->bind(
            BDELogRepository::class,
            function () {
                return new EloquentBDELogRepository(new BDELog());
            }
        );
        $this->app->bind(
            MarkingKeyLogRepository::class,
            function () {
                return new EloquentMarkingKeyLogRepository(new MarkingKeyLog());
            }
        );
        $this->app->bind(
            FinalTestResultRepository::class,
            function () {
                return new EloquentFinalTestResultRepository(new FinalTestResult());
            }
        );
        $this->app->bind(
            FinalTestLogRepository::class,
            function () {
                return new EloquentFinalTestLogRepository(new FinalTestLog());
            }
        );
        $this->app->bind(
            TrainingLocationRepository::class,
            function () {
                return new EloquentTrainingLocationRepository(new TrainingLocation());
            }
        );
        //course binding
        $this->app->bind(
            CourseRepository::class,
            function () {
                $repository = new EloquentCourseRepository(new Course());
                if (!config('app.cache')) {
                    return $repository;
                }
                return new CacheCourseDecorator($repository);
            }
        );

        //purchase binding
        $this->app->bind(
            PurchaseRepository::class,
            function () {
                return new EloquentPurchaseRepository(new Purchase());
            }
        );

        //transaction binding
        $this->app->bind(
            TransactionRepository::class,
            function () {
                return new EloquentTransactionRepository(new Transaction());
            }
        );

        //saved Location binding
        $this->app->bind(
            SavedLocationRepository::class,
            function () {
                return new EloquentSavedLocationRepository(new SavedLocation());
            }
        );

        //Rental Request binding
        $this->app->bind(
            RentalRequestRepository::class,
            function () {
                return new EloquentRentalRequestRepository(new RentalRequest());
            }
        );
    }

    /**
     * Register the console commands
     */
    private function registerCommands()
    {
        $this->commands([
            CreateInstructorCommand::class
        ]);
    }

    public function boot()
    {
        $this->publishConfig('drivisa', 'config');
        $this->publishConfig('drivisa', 'permissions');
        $this->registerMiddleware($this->app['router']);
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }

    public function registerMiddleware(Router $router)
    {
        foreach ($this->routeMiddleware as $alias => $class) {
            $router->aliasMiddleware($alias, $class);
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }
}
