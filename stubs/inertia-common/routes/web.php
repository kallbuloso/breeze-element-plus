<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});
// Esta é uma rota de exemplo para exibir páginas de erro personalizadas durante o desenvolvimento. Ela só será registrada se o ambiente for de teste (por exemplo, quando o domínio termina com ".test").
if (str_ends_with((string) parse_url(env('APP_URL'), PHP_URL_HOST), '.test')) {
    Route::get('/_errors/{status}', function (int $status) {
        return Inertia::render('Error', ['status' => $status])
            ->toResponse(request())
            ->setStatusCode($status);
    })->whereIn('status', [403, 404, 500, 503])->name('errors.preview');
}

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
