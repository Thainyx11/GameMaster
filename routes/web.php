<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\InstructionsController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// ============================================
// ROUTES PUBLIQUES
// ============================================

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

// Pages légales
Route::get('/mentions-legales', function () {
    return Inertia::render('Legal/MentionsLegales');
})->name('legal.mentions');

Route::get('/politique-confidentialite', function () {
    return Inertia::render('Legal/PolitiqueConfidentialite');
})->name('legal.privacy');

Route::get('/cookies', function () {
    return Inertia::render('Legal/Cookies');
})->name('legal.cookies');

// ============================================
// ROUTES PROTÉGÉES (utilisateurs connectés)
// ============================================

Route::middleware(['auth', 'verified'])->group(function () {
    
    // Chat
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/{conversationId}', [ChatController::class, 'index'])->name('chat.show');
    
    // Actions conversations
    Route::post('/chat/conversations', [ChatController::class, 'createConversation'])->name('chat.create');
    Route::delete('/chat/conversations/{conversation}', [ChatController::class, 'deleteConversation'])->name('chat.delete');
    Route::patch('/chat/conversations/{conversation}/model', [ChatController::class, 'updateModel'])->name('chat.updateModel');
    
    // Envoi de message (SSE streaming)
    Route::post('/chat/message', [ChatController::class, 'sendMessage'])->name('chat.send');

    // Instructions personnalisées
    Route::get('/instructions', [InstructionsController::class, 'edit'])->name('instructions.edit');
    Route::put('/instructions', [InstructionsController::class, 'update'])->name('instructions.update');

    // Profil (généré par Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Routes d'authentification (Breeze)
require __DIR__.'/auth.php';