<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $data = [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $request->user(),
                'userRoles' => $request->user() ? $request->user()->roles->pluck('name') : [],
                'userPermissions' => $request->user() ? $request->user()->getPermissionsViaRoles()->pluck('name') : [],
            ],
            'ziggy' => [...(new Ziggy())->toArray(), 'location' => $request->path()],
        ];

        return $this->transformKeysToCamelCase($data);
    }

    /**
     * Transforma recursivamente las claves de snake_case a camelCase
     */
    private function transformKeysToCamelCase($data)
    {
        if (is_array($data)) {
            $result = [];
            foreach ($data as $key => $value) {
                $camelKey = Str::camel($key);
                $result[$camelKey] = $this->transformKeysToCamelCase($value);
            }
            return $result;
        }

        if (is_object($data)) {
            if (method_exists($data, 'toArray')) {
                return $this->transformKeysToCamelCase($data->toArray());
            }

            $result = new \stdClass();
            foreach (get_object_vars($data) as $key => $value) {
                $camelKey = Str::camel($key);
                $result->$camelKey = $this->transformKeysToCamelCase($value);
            }
            return $result;
        }

        return $data;
    }
}
