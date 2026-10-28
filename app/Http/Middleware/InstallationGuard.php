<?php
namespace App\Http\Middleware;
use App\Services\Installer\InstallationState;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
class InstallationGuard
{
    public function __construct(private readonly InstallationState $state) {}
    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->state->enabled()) {
            return $next($request);
        }
        $installerRoute = $request->is('install', 'install/*');
        if ($this->state->isInstalled()) {
            abort_if($installerRoute, 404);

            return $next($request);
        }
        try {
            $this->state->prepareEnvironment();
        } catch (\RuntimeException $e) {
            abort(500, $e->getMessage());
        }
        if ($installerRoute || $request->is('toggle-theme')) {
            return $next($request);
        }
        return redirect()->route('install.index');
    }
}
