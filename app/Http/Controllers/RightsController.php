<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Validator;
use WykoCommon\Services\UserService;
use App\Models\Log;
use App\Models\User;

class RightsController extends Controller
{
    public function search(Request $request,
            UserService $userService) {
        $user = $userService->getCurrentUser();

        if ($user === null || $user->rights < 99) {
            return response(view('errors/404'), 404);
        }

        $validator = Validator::make($request->all(), [
            'nick' => 'required'
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $request->session()->flash('flashError', implode("<br />", $messages));

            return redirect()->back()->withInput();
        }

        $subject = User::where('nick', $request->get('nick'))->first();

        if ($subject === null) {
            $content = @file_get_contents('http://www.wykop.pl/ludzie/' . $request->get('nick'));

            $matches = [];

            if (empty($content) || preg_match('/<img class="avatar (.*?) full" src="(.*?)" title="(.*?)"/', $content, $matches) !== 1) {
                $request->session()->flash('flashError', 'Niepoprawny nick');

                return redirect()->back()->withInput();
            } else {
                $subject = new User();

                $subject->nick = $matches[3];
                $subject->avatar_url = $matches[2];
                $subject->created_count = 0;
                $subject->joined_count = 0;
                $subject->color = 0;

                if ($matches[1] === 'male') {
                    $subject->sex = 1;
                } else if ($matches[1] === 'female') {
                    $subject->sex = 2;
                } else {
                    $subject->sex = 0;
                }

                $subject->save();

                $request->session()->flash('flashMessage', 'Użytkownik dodany');
            }
        }

        return redirect(route('profileUrl', ['id' => $subject->id]));
    }

    public function profile(Request $request,
            UserService $userService,
            $id) {
        $user = $userService->getCurrentUser();

        if ($user === null || $user->rights < 99) {
            return response(view('errors/404'), 404);
        }

        $subject = User::where('id', $id)->first();

        if ($subject === null) {
            $request->session()->flash('flashError', 'Niepoprawne ID użytkownika');

            return redirect()->back()->withInput();
        }

        return view('rights/profile', array(
            'item' => $subject
        ));
    }

    public function changeRights(Request $request,
            UserService $userService) {
        $user = $userService->getCurrentUser();

        if ($user === null || $user->rights < 99) {
            return response(view('errors/404'), 404);
        }

        $validator = Validator::make($request->all(), [
            'userId' => 'required|integer',
            'rights' => 'required|int|in:2,10,20,99',
            'callOptOut' => 'required|integer|in:0,1'
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $request->session()->flash('flashError', implode("<br />", $messages));

            return redirect()->back()->withInput();
        }

        $subject = User::where('id', $request->get('userId'))->first();

        if ($subject === null) {
            $request->session()->flash('flashError', 'Niepoprawne ID użytkownika');

            return redirect()->back()->withInput();
        }

        if ($subject->rights !== (int) $request->get('rights')) {
            $log = new Log();

            $log->user_id = $user->id;
            $log->subject_id = $subject->id;

            if ($request->get('rights') == 2) {
                $log->type = Log::TYPE_RIGHTS_BANNED;
            } else {
                $log->type = Log::TYPE_RIGHTS_CHANGE;
            }

            $log->save();

            $subject->rights = (int) $request->get('rights');
        }

        if ($subject->call_optout !== (int) $request->get('callOptOut')) {
            $log = new Log();

            $log->user_id = $user->id;
            $log->subject_id = $subject->id;

            if ($request->get('callOptOut') == 1) {
                $log->type = Log::TYPE_RIGHTS_CALL_OPTOUT_ON;
            } else {
                $log->type = Log::TYPE_RIGHTS_CALL_OPTOUT_OFF;
            }

            $log->save();

            $subject->call_optout = (int) $request->get('callOptOut');
        }

        $subject->save();

        Cache::forget('user_' . $subject->id);

        $request->session()->flash('flashSuccess', 'Uprawnienia zmienione');

        return redirect()->back();
    }
}