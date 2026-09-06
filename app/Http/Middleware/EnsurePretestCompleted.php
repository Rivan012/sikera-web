<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePretestCompleted
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->isMahasiswa() && !$user->pretest_completed) {
            // Rute yang diizinkan saat pretest belum selesai
            $allowedRoutes = [
                'pretest.show',
                'pretest.submit',
                'logout',
            ];

            if (!in_array($request->route()->getName(), $allowedRoutes)) {
                return redirect()->route('pretest.show')
                    ->with('warning', 'Silakan selesaikan kuesioner Pre-Test terlebih dahulu untuk membuka akses seluruh modul dan fitur SIKERA.');
            }
        }

        return $next($request);
    }
}
