<?php

namespace Kjamesy\Cms\Middleware;


use Kjamesy\Cms\Models\User;

class ManageContentMiddleware
{
    /**
     * Run the request filter.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        if ( ! User::canManageContent($request->user()) ) {
            return redirect(route('users.profile'));
        }

        return $next($request);
    }

}