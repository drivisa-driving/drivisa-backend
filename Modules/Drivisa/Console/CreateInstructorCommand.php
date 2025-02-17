<?php

namespace Modules\Drivisa\Console;

use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\DB;
use Modules\Drivisa\Entities\Instructor;
use Modules\User\Repositories\UserRepository;
use Modules\User\Repositories\UserTokenRepository;

class CreateInstructorCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:instructor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will create an Instructor.';

    /**
     * @var Application
     */
    protected $application;

    /**
     * Create a new command instance.
     *
     * @param Application $application
     * @return void
     */
    public function __construct(Application $application)
    {
        parent::__construct();
        $this->application = $application;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->createInstructor();
    }

    /**
     * Create instructor
     */
    protected function createInstructor()
    {
        DB::beginTransaction();
        try {
            $info = [
                'first_name' => $this->askForFirstName(),
                'last_name' => $this->askForLastName(),
                'email' => $this->askForEmail(),
                'password' => $this->getHashedPassword(
                    $this->askForPassword()
                ),
            ];

            $user = $this->application->make(UserRepository::class)->createWithRolesFromCli($info, [1], true);
            $this->application->make(UserTokenRepository::class)->generateFor($user->id);

            $this->info('Please wait while the admin account is configured...');

            $instructor = Instructor::query()->create([
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'user_id' => $user->id,
                'verified' => 1,
                'no' => Carbon::now()->timestamp
            ]);

            DB::commit();
            $this->info('Instructor created successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            $this->error('Something went wrong!');
        }
    }

    /**
     * @return string
     */
    private function askForFirstName()
    {
        do {
            $firstname = $this->ask('Enter the instructor first name');
            if ($firstname == '') {
                $this->error('First name is required');
            }
        } while (!$firstname);

        return $firstname;
    }

    /**
     * @return string
     */
    private function askForLastName()
    {
        do {
            $lastname = $this->ask('Enter the instructor last name');
            if ($lastname == '') {
                $this->error('Last name is required');
            }
        } while (!$lastname);

        return $lastname;
    }

    /**
     * @return string
     */
    private function askForEmail()
    {
        do {
            $email = $this->ask('Enter the user email address');
            if ($email == '') {
                $this->error('Email is required');
            }
        } while (!$email);

        return $email;
    }

    /**
     * @param $password
     * @return mixed
     */
    private function getHashedPassword($password)
    {
        return $password;
    }

    /**
     * @return string
     */
    private function askForPassword()
    {
        do {
            $password = $this->askForFirstPassword();
            $passwordConfirmation = $this->askForPasswordConfirmation();
            if ($password != $passwordConfirmation) {
                $this->error('Password confirmation doesn\'t match. Please try again.');
            }
        } while ($password != $passwordConfirmation);

        return $password;
    }

    /**
     * @return string
     */
    private function askForFirstPassword()
    {
        do {
            $password = $this->secret('Enter a password');
            if ($password == '') {
                $this->error('Password is required');
            }
        } while (!$password);

        return $password;
    }

    /**
     * @return string
     */
    private function askForPasswordConfirmation()
    {
        do {
            $passwordConfirmation = $this->secret('Please confirm your password');
            if ($passwordConfirmation == '') {
                $this->error('Password confirmation is required');
            }
        } while (!$passwordConfirmation);

        return $passwordConfirmation;
    }
}
