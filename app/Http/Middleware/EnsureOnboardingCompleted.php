<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOnboardingCompleted
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        $profile = $user->profile;
        $completed = $profile && $profile->onboarding_completed;

        if ($completed && $request->routeIs('onboarding')) {
            return redirect()->route('beranda');
        }

        if (! $completed && ! $request->routeIs('onboarding')) {
            return redirect()->route('onboarding');
        }

        return $next($request);
    }
}
