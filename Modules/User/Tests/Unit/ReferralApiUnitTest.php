<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\User\Services\ReferralCodeGenerator;

uses(RefreshDatabase::class)->in(__DIR__);

it('can generate a code', function () {
    $generator = new ReferralCodeGenerator();
    $this->assertNotNull($generator->generate());
});


it('can generate a unique code', function () {
    $generator = new ReferralCodeGenerator();

    $code1 = $generator->generate();

    $code2 = $generator->generate();

    $this->assertNotSame($code1, $code2);
});