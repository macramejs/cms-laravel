<?php

namespace Macrame\Admin\Pages\Traits;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Macrame\Tree\Traits\IsTree;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Route as RouteFactory;

trait IsPage
{
    use IsTree;

    /**
     * Generate the routes for the pages.
     *
     * @return void
     */
    public static function routes()
    {
        try {
            return static::where('parent_id', null)
                ->get()
                ->map(function (self $site) {
                    if (! $site->is_live) {
                        return;
                    }

                    return $site->getRoute();
                });
        } catch (QueryException $e) {
        }
    }

    /**
     * Get the page model from the given request.
     *
     * @param Request $request
     * @return self
     */
    public static function fromRequestOrFail(Request $request)
    {
        $id = last(explode('.', $request->route()->getName()));

        if (!$id) {
            abort(404);
        }

        return static::findOrFail($id);
    }

    /**
     * Get the associated route.
     *
     * @return Route
     */
    public function getRoute(): Route
    {
        return RouteFactory::middleware('web')
            ->get($this->getFullSlug(), $this->controller)
            ->name("site.{$this->id}");
    }

    /**
     * Get the route action.
     *
     * @return void
     */
    public function getRouteAction(): string | Closure
    {
        return $this->controller;
    }

    /**
     * Get the full slug of the page.
     *
     * @return string
     */
    public function getFullSlug(): string
    {
        if (! $this->parent) {
            return '/'.$this->slug;
        }

        return $this->parent->getFullSlug().'/'.$this->slug;
    }
}
