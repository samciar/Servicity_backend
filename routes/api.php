<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BidController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;

// Public routes
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// Categories
Route::get('categories', [CategoryController::class, 'index']);
Route::get('categories/{id}', [CategoryController::class, 'show']);
Route::get('categories/{id}/skills', [CategoryController::class, 'byCategory']);

// Skills
Route::get('skills', [SkillController::class, 'index']);
Route::get('skills/{id}', [SkillController::class, 'show']);
Route::get('skills/search', [SkillController::class, 'search']);

// Protected routes
Route::middleware(['auth:sanctum'])->group(function () {
    // Auth
    Route::post('logout', [AuthController::class, 'logout']);
    
    // User routes
    Route::get('user', [UserController::class, 'me']);
    Route::put('user/profile', [UserController::class, 'updateProfile']);
    Route::apiResource('users', UserController::class)->except(['store']);
    Route::get('users/clients', [UserController::class, 'clients']);
    Route::get('users/taskers', [UserController::class, 'taskers']);
    Route::get('users/admins', [UserController::class, 'admins']);

    // Task routes
    Route::apiResource('tasks', TaskController::class);
    Route::put('tasks/{id}/status', [TaskController::class, 'updateStatus']);
    Route::get('tasks/search', [TaskController::class, 'search']);
    Route::get('tasks/urgent', [TaskController::class, 'urgent']);
    Route::get('user/tasks', [TaskController::class, 'clientTasks']);

    // Bid routes
    Route::apiResource('bids', BidController::class);
    Route::post('bids/{id}/accept', [BidController::class, 'accept']);
    Route::post('bids/{id}/reject', [BidController::class, 'reject']);
    Route::post('bids/{id}/withdraw', [BidController::class, 'withdraw']);
    Route::get('bids/pending', [BidController::class, 'pending']);
    Route::get('bids/accepted', [BidController::class, 'accepted']);

    // Booking routes
    Route::apiResource('bookings', BookingController::class);
    Route::post('bookings/{id}/progress', [BookingController::class, 'markAsInProgress']);
    Route::post('bookings/{id}/complete', [BookingController::class, 'complete']);
    Route::post('bookings/{id}/cancel', [BookingController::class, 'cancel']);
    Route::get('bookings/scheduled', [BookingController::class, 'scheduled']);
    Route::get('bookings/active', [BookingController::class, 'active']);
    Route::get('bookings/completed', [BookingController::class, 'completed']);

    // Payment routes
    Route::apiResource('payments', PaymentController::class);
    Route::post('payments/{id}/complete', [PaymentController::class, 'markAsCompleted']);
    Route::post('payments/{id}/fail', [PaymentController::class, 'markAsFailed']);
    Route::post('payments/{id}/refund', [PaymentController::class, 'refund']);
    Route::get('payments/completed', [PaymentController::class, 'completed']);
    Route::get('payments/methods', [PaymentController::class, 'paymentMethods']);
    Route::get('payments/currencies', [PaymentController::class, 'currencies']);

    // Review routes
    Route::apiResource('reviews', ReviewController::class);
    Route::get('reviews/user/{id}', [ReviewController::class, 'forUser']);
    Route::get('reviews/by-user/{id}', [ReviewController::class, 'byUser']);
    Route::get('reviews/user/{id}/average', [ReviewController::class, 'averageRating']);
    Route::get('reviews/user/{id}/high-rated', [ReviewController::class, 'highRated']);

    // Message routes
    Route::apiResource('messages', MessageController::class);
    Route::get('messages/conversation/{userId}', [MessageController::class, 'conversation']);
    Route::post('messages/{id}/read', [MessageController::class, 'markAsRead']);
    Route::get('messages/unread', [MessageController::class, 'unread']);
    Route::get('messages/latest/{userId}', [MessageController::class, 'latestMessage']);

    // Notification routes
    Route::apiResource('notifications', NotificationController::class);
    Route::post('notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('notifications/{id}/unread', [NotificationController::class, 'markAsUnread']);
    Route::post('notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
    Route::get('notifications/unread-count', [NotificationController::class, 'unreadCount']);

    // Category management routes (protected)
    Route::post('categories', [CategoryController::class, 'store']);
    Route::put('categories/{id}', [CategoryController::class, 'update']);
    Route::delete('categories/{id}', [CategoryController::class, 'destroy']);
    Route::get('categories/with-skills', [CategoryController::class, 'withSkills']);
    Route::get('categories/with-active-tasks', [CategoryController::class, 'withActiveTasks']);

    // Skill management routes (protected)
    Route::post('skills', [SkillController::class, 'store']);
    Route::put('skills/{id}', [SkillController::class, 'update']);
    Route::delete('skills/{id}', [SkillController::class, 'destroy']);
    Route::get('skills/with-taskers', [SkillController::class, 'withTaskers']);
    Route::get('skills/{id}/proficiency', [SkillController::class, 'averageProficiency']);
});
