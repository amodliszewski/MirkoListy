<?php
/**
 * Created by XZ Software.
 * Smart code for smart wallet
 * http://xzsoftware.pl
 * User adrianmodliszewski
 * Date: 21/01/2019
 * Time: 19:43
 */

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use WykoCommon\Services\UserService as CommonUserService;

class UserService extends CommonUserService
{
    /** @var Request */
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
        parent::__construct($request);
    }

    /**
     * Gets current user based on token
     *
     * @return null|User
     */
    public function getCurrentApiUser()
    {
        if (!$this->request->headers->has('X-Token')) {
            return null;
        }

        $user = User::where('api_key', '=', (string) $this->request->headers->get('X-Token'))->first();

        if (!is_object($user)) {
            return null;
        }

        return $user;
    }

    /**
     * Gets current user based on token / session
     * @return null|User
     */
    public function getCurrentUser()
    {
        $user = $this->getCurrentApiUser();
        if ($user === null) {
            return parent::getCurrentUser();
        }
        return $user;
    }
}