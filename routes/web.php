<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\Auth\AuthenticationController;
use App\Http\Controllers\ContactTimelineController;
use App\Http\Controllers\DealController;
use App\Http\Controllers\DuplicateController;
use App\Http\Controllers\InboxController;
use App\Http\Controllers\InsightsController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SampleDataController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SetupController;
use App\Http\Controllers\WebFormController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'marketing')->name('home');

// Public lead capture form (LeadBooster web form).
Route::get('f/contact', [WebFormController::class, 'show'])->name('web-form.show');
Route::post('f/contact', [WebFormController::class, 'store'])->name('web-form.store');

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticationController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthenticationController::class, 'login']);
    Route::get('register', [AuthenticationController::class, 'showRegister'])->name('register');
    Route::post('register', [AuthenticationController::class, 'register']);
});

Route::post('logout', [AuthenticationController::class, 'logout'])
    ->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('setup', [SetupController::class, 'index'])->name('setup.index');
    Route::post('setup/{task}/complete', [SetupController::class, 'complete'])->name('setup.complete');
    Route::delete('setup/{task}/complete', [SetupController::class, 'uncomplete'])->name('setup.uncomplete');

    Route::get('search', SearchController::class)->name('search');

    // Contacts
    Route::get('contacts', fn () => redirect()->route('people.index'))->name('contacts.index');
    Route::resource('people', PersonController::class)->except(['create', 'edit']);
    Route::resource('organizations', OrganizationController::class)->except(['create', 'edit']);
    Route::get('contacts/timeline', ContactTimelineController::class)->name('contacts.timeline');
    Route::get('contacts/duplicates', [DuplicateController::class, 'index'])->name('contacts.duplicates');
    Route::post('contacts/duplicates', [DuplicateController::class, 'merge'])->name('contacts.duplicates.merge');

    // Activities
    Route::get('activities', [ActivityController::class, 'index'])->name('activities.index');
    Route::get('activities/calendar', [ActivityController::class, 'calendar'])->name('activities.calendar');
    Route::post('activities', [ActivityController::class, 'store'])->name('activities.store');
    Route::patch('activities/{activity}', [ActivityController::class, 'update'])->name('activities.update');
    Route::post('activities/{activity}/toggle', [ActivityController::class, 'toggle'])->name('activities.toggle');
    Route::delete('activities/{activity}', [ActivityController::class, 'destroy'])->name('activities.destroy');

    // Deals
    Route::get('deals', [DealController::class, 'index'])->name('deals.index');
    Route::get('deals/list', [DealController::class, 'list'])->name('deals.list');
    Route::post('deals', [DealController::class, 'store'])->name('deals.store');
    Route::get('deals/{deal}', [DealController::class, 'show'])->name('deals.show');
    Route::patch('deals/{deal}', [DealController::class, 'update'])->name('deals.update');
    Route::post('deals/{deal}/move', [DealController::class, 'move'])->name('deals.move');
    Route::post('deals/{deal}/status', [DealController::class, 'status'])->name('deals.status');
    Route::delete('deals/{deal}', [DealController::class, 'destroy'])->name('deals.destroy');

    // Leads
    Route::get('leads', [LeadController::class, 'index'])->name('leads.index');
    Route::get('leads/web-forms', [WebFormController::class, 'settings'])->name('leads.web-forms');
    Route::post('leads', [LeadController::class, 'store'])->name('leads.store');
    Route::get('leads/{lead}', [LeadController::class, 'show'])->name('leads.show');
    Route::patch('leads/{lead}', [LeadController::class, 'update'])->name('leads.update');
    Route::post('leads/{lead}/archive', [LeadController::class, 'archive'])->name('leads.archive');
    Route::post('leads/{lead}/convert', [LeadController::class, 'convert'])->name('leads.convert');
    Route::delete('leads/{lead}', [LeadController::class, 'destroy'])->name('leads.destroy');

    Route::get('insights', [InsightsController::class, 'index'])->name('insights.index');

    // Sales inbox
    Route::get('inbox', [InboxController::class, 'index'])->name('inbox.index');
    Route::get('inbox/{thread}', [InboxController::class, 'show'])->name('inbox.show');
    Route::post('inbox/{thread}/reply', [InboxController::class, 'reply'])->name('inbox.reply');
    Route::post('inbox/{thread}/star', [InboxController::class, 'star'])->name('inbox.star');
    Route::post('inbox/{thread}/archive', [InboxController::class, 'archive'])->name('inbox.archive');

    Route::post('notes', [NoteController::class, 'store'])->name('notes.store');
    Route::delete('notes/{note}', [NoteController::class, 'destroy'])->name('notes.destroy');

    Route::delete('sample-data', SampleDataController::class)->name('sample-data.destroy');

    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
});
