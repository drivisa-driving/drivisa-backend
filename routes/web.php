<?php


Route::get('/.well-known/assetlinks', function () {
    return response()->file(resource_path('views/assetlinks.json'));
});

Route::get('/.well-known/apple-app-site-association', function () {
    return response()->file(resource_path('views/apple-app-site-association.json'));
});


Route::post('/stripe/webhook', [\Modules\Drivisa\Http\Controllers\StripeWebhookController::class, 'handle']);
Route::get('/export-trainee', [\Modules\Drivisa\Http\Controllers\Api\admin\TraineeAdminController::class, 'export']);

//Route::view('/{any?}/{child?}/{subchild?}/{subchild2?}', 'main');

Route::any('/staging', function () {
    return 'success';
});
Route::any('/truncate-log', function () {
    \App\NotificationLog::truncate();
    return 'success';
});
Route::any('/token-expire', function () {
   \Modules\User\Entities\UserToken::query()->update(['expired_at'=>now()]);
    return 'success';
});


Route::get('download/{visitType}', [\Modules\Drivisa\Http\Controllers\Api\VisitCounterController::class, 'visit']);

Route::view('/{any}', 'main')
    ->where('any', '^((?!api).)*');
