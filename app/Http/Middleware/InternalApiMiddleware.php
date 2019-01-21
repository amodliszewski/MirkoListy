<?php
/**
 * Created by XZ Software.
 * Smart code for smart wallet
 * http://xzsoftware.pl
 * User adrianmodliszewski
 * Date: 21/01/2019
 * Time: 17:58
 */

namespace App\Http\Middleware;

use App\Services\UserService;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Models\User;

class InternalApiMiddleware
{
    private $request = null;

    public function __construct(UserService $userService) {
        $this->userService = $userService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        if (!$this->userService->canAdd()) {
            $user = $this->userService->getCurrentApiUser();

            if ($user === null) {
                return new JsonResponse('Wrong token', Response::HTTP_BAD_REQUEST);
            }

            if ($user === null || $user->rights <= User::RIGHTS_BLOCKED_ADDING) {
                return new JsonResponse('Adding blocked', Response::HTTP_FORBIDDEN);
            }

            if ($user->color === 0 || $user->color === 1001 || $user->color === 1002) {
                return new JsonResponse("You can't currently write on Wykop", Response::HTTP_FORBIDDEN);
            }
        }

        return $next($request);
    }
}
