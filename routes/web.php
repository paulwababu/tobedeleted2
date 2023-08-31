<?php

use App\Http\Controllers\AgentController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CountyController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UnitTypeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WalletTransactionController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::post('/onboard', [OnboardingController::class, 'create'])->name('onboard.create');

Auth::routes();

Route::group([
    'middleware' => ['auth:web']
], function () {
    Route::middleware(['role:owner|agent|admin|tenant'])->group(function () {
        // load counties
        Route::post('load-counties', [CountyController::class, 'load'])->name('county.load');
        // load unit types
        Route::post('load-unit-types', [UnitTypeController::class, 'load'])->name('county.load');
        // dashboard page
        Route::get('dashboard', [UserController::class, 'dashboardPage'])->name('user.dashboard-page');
        // logout
        Route::get('logout', [LoginController::class, 'logout'])->name('user.logout');

        /*
         * manage company
         */
        Route::prefix('company')->group(function () {
            Route::get('update', [CompanyController::class, 'updatePage'])->name('company.update-page');
            Route::post('{company}/load', [CompanyController::class, 'loadCompanyMetadata'])->name('company.load-company-metadata');
            Route::post('{company}/update', [CompanyController::class, 'update'])->name('company.update');
        });

        /*
         * manage wallet
         */
        Route::prefix('wallet-transactions')->group(function () {
            Route::get('manage', [WalletTransactionController::class, 'managePage'])->name('wallet-transaction.manage-page');
            Route::post('load-for-company/load', [WalletTransactionController::class, 'loadForCompany'])->name('wallet-transaction.load-for-company');
            Route::post('top-up/wallet/{company}', [WalletTransactionController::class, 'topUpWallet'])->name('wallet-transaction.top-up-wallet');
        });
    });

    /*
     * Tenant Routes
     */
    Route::middleware(['role:tenant'])->prefix('tenant')->group(function () {
        Route::prefix('units')->group(function () {
//            Route::get('browse', [UnitController::class, 'browsePage'])->name('unit.browse-page');
//            Route::post('browse', [UnitController::class, 'browse'])->name('unit.browse');
            Route::get('my-residences', [TenantController::class, 'myResidencesPage'])->name('tenant.my-residences-page');
            Route::post('my-residences/load', [TenantController::class, 'loadMyResidences'])->name('tenant.load-my-residences');
            Route::post('confirm-checkin-request/{tenant}', [TenantController::class, 'confirmCheckInRequest'])->name('tenant.confirm-checkin-request');
            Route::post('pay-rent-via-mpesa/{tenant}', [TenantController::class, 'payRentViaMpesa'])->name('tenant.pay-rent-via-mpesa');
            Route::post('issue-vacation-notice/{tenant}', [TenantController::class, 'issueVacationNotice'])->name('tenant.issue-vacation-notice');
            Route::get('tenant-transactions', [TenantController::class, 'transactionsPage'])->name('tenant.transactions-page');
            Route::post('tenant-transactions/load', [TenantController::class, 'loadTransactions'])->name('tenant.load-transactions');
            Route::get('rental-payments', [TenantController::class, 'manageRentalPaymentsPage'])->name('tenant.manage-rental-payments-page');
            Route::post('rental-payments/load', [TenantController::class, 'loadRentalPayments'])->name('tenant.load-rental-payments');
        });
    });

    /*
     * Owner & Agent Routes
     */
    Route::middleware(['role:owner|agent|admin'])->prefix('owner')->group(function () {
        Route::prefix('properties')->group(function () {
            Route::get('manage', [PropertyController::class, 'managePage'])->name('property.manage-page');
            Route::post('load', [PropertyController::class, 'load'])->name('property.load');
            Route::post('create', [PropertyController::class, 'create'])->name('property.create');
            Route::post('{property}/update', [PropertyController::class, 'update'])->name('property.create');
        });

        Route::prefix('units')->group(function () {
            Route::get('{property}/manage-units', [UnitController::class, 'managePage'])->name('unit.manage-page');
            Route::post('{property}/load', [UnitController::class, 'load'])->name('unit.load');
            Route::post('{property}/create', [UnitController::class, 'create'])->name('unit.create');
            Route::post('{property}/update/{unit}', [UnitController::class, 'update'])->name('unit.update');
            Route::post('photos/{unit}/create', [PhotoController::class, 'create'])->name('photo.create');
            Route::delete('photos/{photo}/delete', [PhotoController::class, 'delete'])->name('photo.delete');
        });

        Route::prefix('tenants')->group(function () {
            Route::get('manage', [TenantController::class, 'managePage'])->name('tenant.manage-page');
            Route::post('load', [TenantController::class, 'load'])->name('tenant.load');
            Route::post('{unit}/create', [TenantController::class, 'create'])->name('tenant.create');

            Route::prefix('bills')->group(function () {
                Route::post('{tenant}/create', [BillController::class, 'create'])->name('bill.create');
                Route::get('{tenant}/history', [BillController::class, 'historyPage'])->name('bill.history-page');
            });
        });

        Route::prefix('inquiries')->group(function () {
            Route::get('manage', [InquiryController::class, 'managePage'])->name('inquiry.manage-page');
            Route::post('load', [InquiryController::class, 'load'])->name('inquiry.load');
            Route::post('{property}/{inquiry}/respond', [InquiryController::class, 'respond'])->name('inquiry.respond');
        });

        Route::prefix('messages')->group(function () {
            Route::get('send-single', [MessageController::class, 'sendSinglePage'])->name('message.send-single-page');
            Route::post('send-single', [MessageController::class, 'sendSingle'])->name('message.send-single');
            Route::get('outgoing', [MessageController::class, 'outgoingSmsPage'])->name('message.outgoing-sms-page');
            Route::post('outgoing/load', [MessageController::class, 'loadOutgoing'])->name('message.load-outgoing');
        });
    });

    Route::middleware(['role:owner'])->prefix('owner')->group(function () {
        Route::prefix('agents')->group(function () {
            Route::get('manage', [AgentController::class, 'manageOrderAgentsPage'])->name('agent.manage-owner-agents-page');
            Route::post('load', [AgentController::class, 'loadOwnerAgents'])->name('agent.load-owner-agents');
            Route::post('create', [AgentController::class, 'create'])->name('agent.create');
            Route::post('{user}/update', [AgentController::class, 'update'])->name('agent.update');
            Route::post('{user}/approve', [AgentController::class, 'approve'])->name('agent.approve');
            Route::post('{user}/suspend', [AgentController::class, 'suspend'])->name('agent.suspend');
        });
    });

    Route::middleware(['role:owner|agent'])->prefix('owner')->group(function () {
        Route::prefix('rental-payments')->group(function () {
            Route::get('manage', [OwnerController::class, 'manageRentalPaymentsPage'])->name('owner.rental-payments-page');
            Route::post('load', [OwnerController::class, 'loadOwnerRentalPayments'])->name('agent.load-owner-rental-payments');
        });
    });
});
