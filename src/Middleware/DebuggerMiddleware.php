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
        $return = null;
        if ( $request->hasAny(['created-by-me', 'c-b-m']) || $request->hasHeader('created-by-me') || $request->hasHeader('c-b-m') ) {
            $return ??= $next($request);
            /** @var \Model $model */
            $model = collect(currentRoute()->originalParameters())->take(1)->mapWithKeys(function ($id, $class) {
                try {
                    $cName = (new \Facade\Ignition\Support\ComposerClassMap)->searchClassMap(studly_case($class));
                    $_class = app($cName)->find($id);
                } catch (\Exception $exception) {
                    return [];
                }

                return [
                    $class => $_class
                ];
            })->first();

            $cbm = $request->get('created-by-me', $request->get('c-b-m', $request->header('created-by-me', $request->header('c-b-m', null))));
            if ( $model ) {
                $model->setCreatedBy(UserId($cbm ?: $model->created_by), true);

                return response()->json([
                    'model' => $model->showCreatorColumn()
                ]);
            }

            return response()->json(
                "No Current Model !"
            );
        }

        if ( $request->hasAny(['show-route', 's-r']) || $request->hasHeader('show-route') || $request->hasHeader('s-r') ) {
            $return ??= $next($request);
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
                try {
                    $uses = currentRoute()->action['uses'];
                } catch (\Exception $exception) {
                    $return = $next($request);
                    $uses = currentRoute()->action['uses'];
                }

                $uses = str_ireplace(['//', '\\\\'], ['/', '/'], str_ireplace('@', '::', $uses));
                $uses = str_ireplace(['/', '\\'], '/', $uses);

                return response()->json([
                    'Class' => $uses,
                    'Info' => [
                        'Controller' => class_basename(currentController()),
                        'ActionName' => currentActionName(),
                        'RouteName' => ($_current = Route::current()) ? $_current->getName() : null,
                        'Uri' => currentRoute()->uri,
                    ],
                    'Action' => currentRoute()->action,
                    'Methods' => implode(", ", currentRoute()->methods),
                    'Request' => $request->all(),
                    'Debug' => $debug,
                ]);
            }

            try {
                echo "<b>Class: </b>";
                $uses = currentRoute()->action['uses'];
                $uses = is_string($uses) ? $uses : gettype($uses);
                dump(str_ireplace('@', '::', $uses));
            } catch (\Exception $exception) {

            }


            try {
                $controller = class_basename(currentController());
            } catch (\Exception $exception) {
                $controller = "-";
            }

            try {
                $action_name = currentActionName();
            } catch (\Exception $exception) {
                $action_name = '-';
            }

            try {
                $route_name = ($_current = Route::current()) ? $_current->getName() : null;
            } catch (\Exception $exception) {
                $route_name = '-';
            }

            try {
                $uri = currentRoute()->uri;
            } catch (\Exception $exception) {
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
                $request->all()
            );
            echo "<hr><b>Debug: </b>";
            dump(
                $debug
            );
            dE();
        }

        $return ??= $next($request);
        return $return;
    }

}
