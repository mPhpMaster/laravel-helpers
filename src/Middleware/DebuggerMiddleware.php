<?php

namespace mPhpMaster\Support\Middleware;

use Closure;
use Illuminate\Support\Facades\Route;

class DebuggerMiddleware
{
    /**
     * The application implementation.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
//    protected $app;
    /** @var \Illuminate\Routing\Route $route */
//    protected $route;

    /**
     * Create a new middleware instance.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     *
     * @return void
     */
    public function __construct(/*Application $app*/)
    {
//        $this->app = $app;
    }


    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ( $request->hasAny(['show-route', 's-r']) || $request->hasHeader('show-route') || $request->hasHeader('s-r') ) {

            $debug = $file = $line = $method = null;
            /** @var string|null $string */
            $string = null;
            $debug = @debug_backtrace();
            $debug = traceInfo($debug)['debugTrace'];
            $debug = collect($debug)->filter(function ($trace) {
                return !starts_with($trace['file'], [
                    base_path('vendor'),
                    public_path('/')
                ]);
            })->all();

            if ( $request->wantsJson() ) {
                return response()->json([
                    'Class' => str_ireplace('@', '::', currentRoute()->action['uses']),
                    'Info' => [
                        'Controller' => class_basename(currentController()),
                        'ActionName' => currentActionName(),
                        'RouteName' => Route::current()->getName(),
                        'Uri' => currentRoute()->uri,
                    ],
                    'Action' => currentRoute()->action,
                    'Methods' => implode(", ", currentRoute()->methods),
                    'Request' => request()->all(),
                    'Debug' => $debug,
                ]);
            }

            try {
                echo "<b>Class: </b>";
                $uses = currentRoute()->action['uses'];
                $uses = is_string($uses) ? $uses : gettype($uses);
                dump(str_ireplace('@', '::', $uses));
            } catch (\Exception $exception ) {

            }


            try {
                $controller = class_basename(currentController());
            } catch (\Exception $exception ) {
                $controller = "-";
            }

            try {
                $action_name = currentActionName();
            } catch (\Exception $exception ) {
                $action_name = '-';
            }

            try {
                $route_name = Route::current()->getName();
            } catch (\Exception $exception ) {
                $route_name = '-';
            }

            try {
                $uri = currentRoute()->uri;
            } catch (\Exception $exception ) {
                $uri = '-';
            }


            echo "<hr><b>Info: </b>";
            dump([
                    'Controller' => $controller,
                    'ActionName' => $action_name,
                    'RouteName' => $route_name,
                    'Uri' => $uri,
                ]
            );


            echo "<hr><b>Action: </b>";
            dump(currentRoute()->action);

            echo "<hr><b>Methods: </b>";
            dump(implode(", ", currentRoute()->methods));

            echo "<hr><b>Request: </b>";
            dump(
                request()->all()
            );
            echo "<hr><b>Debug: </b>";
            dump(
                $debug
            );
            dE();
        }

        return $next($request);
    }

}
