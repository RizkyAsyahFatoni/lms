<?php

use App\Http\Controllers\Api\AsnIntegrationApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes for ASN & External Government Integrations (SIMPEG, SIASN, e-Kinerja)
|--------------------------------------------------------------------------
| Protected with Bearer Token middleware against api_keys table.
| Example Header: Authorization: Bearer berau_lms_live_xxxx...
*/
Route::prefix('v1/asn')->middleware('auth.apikey')->group(function () {
    Route::get('/participants', [AsnIntegrationApiController::class, 'participants'])->name('api.asn.participants');
    Route::get('/participants/{nipOrId}', [AsnIntegrationApiController::class, 'participantDetail'])->name('api.asn.participant.detail');
    Route::get('/certificates/verify/{hashOrNumber?}', [AsnIntegrationApiController::class, 'verifyCertificate'])->name('api.asn.certificate.verify');
    Route::get('/stats', [AsnIntegrationApiController::class, 'stats'])->name('api.asn.stats');
});
