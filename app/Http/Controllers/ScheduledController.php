<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use WykoCommon\Services\UserService;
use App\Services\CallService;
use App\Models\ScheduledPost;
use App\Models\User;
use App\Models\Log;

class ScheduledController extends Controller
{
    public function editForm(UserService $userService, $id) {
        $user = $userService->getCurrentUser();

        if ($user === null || $user->rights < User::RIGHTS_EXTENDED) {
            return response(view('errors/404'), 404);
        }

        $entity = ScheduledPost::where('id', '=', $id)
                ->first();

        if ($entity === null) {
            return response(view('errors/404'), 404);
        }

        if ($entity->user_id !== $user->id
                && $user->rights != 99) {
            return response(view('errors/404'), 404);
        }

        return view('scheduled/edit', array(
            'item' => $entity
        ));
    }

    public function edit(Request $request, UserService $userService, $id) {
        $user = $userService->getCurrentUser();

        if ($user === null || $user->rights < User::RIGHTS_EXTENDED) {
            return response(view('errors/404'), 404);
        }

        $entity = ScheduledPost::where('id', '=', $id)
                ->first();

        if ($entity === null) {
            return response(view('errors/404'), 404);
        }

        if ($entity->user_id !== $user->id
                && $user->rights != 99) {
            return response(view('errors/404'), 404);
        }

        $validator = Validator::make($request->all(), [
            'embed' => 'url',
            'content' => 'required|max:9999',
            'post_at' => 'required|date|after:now',
            'spamlists.*' => 'regex:/[0-9A-Za-z]+/',
            'spamlistSex' => 'required|in:0,1,2'
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $request->session()->flash('flashError', implode("<br />", $messages));

            return redirect()->back()->withInput();
        }

        $entity->embed = $request->get('embed');
        $entity->content = $request->get('content');
        $entity->post_at = $request->get('post_at');
        $entity->spamlists = empty($request->get('spamlists')) ? '' : implode(',', $request->get('spamlists'));
        $entity->spamlist_sex = $request->get('spamlistSex');

        $entity->save();

        $log = new Log();

        $log->user_id = $user->id;
        $log->type = Log::TYPE_SCHEDULED_EDITED;
        $log->scheduled_post_id = $entity->id;

        $log->save();

        $request->session()->flash('flashSuccess', 'Zmiany zapisane');

        return redirect()->route('getScheduledItemsUrl');
    }

    public function add(Request $request, UserService $userService, CallService $callService) {
        $user = $userService->getCurrentUser();

        if ($user === null || $user->rights < User::RIGHTS_EXTENDED) {
            return response(view('errors/404'), 404);
        }

        $validator = Validator::make($request->all(), [
            'embed' => 'url',
            'content' => 'required|max:9999',
            'post_at' => 'required|date|after:now',
            'spamlists.*' => 'regex:/[0-9A-Za-z]+/',
            'spamlistSex' => 'required|in:0,1,2'
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $request->session()->flash('flashError', implode("<br />", $messages));

            return redirect()->back()->withInput();
        }

        $item = new ScheduledPost();

        $item->user_id = $user->id;
        $item->embed = $request->get('embed');
        $item->content = $request->get('content');
        $item->post_at = $request->get('post_at');
        $item->user_key = $request->session()->get('wykopUserKey');
        $item->spamlists = empty($request->get('spamlists')) ? '' : implode(',', $request->get('spamlists'));
        $item->spamlist_sex = $request->get('spamlistSex');
        $item->user_call_limit = $callService->getGroupPerComment($request->session()->get('wykopGroup'));

        $item->save();

        $log = new Log();

        $log->user_id = $user->id;
        $log->type = Log::TYPE_SCHEDULED_CREATED;
        $log->scheduled_post_id = $item->id;

        $log->save();

        $request->session()->flash('flashSuccess', 'Wpis zaplanowany');

        return redirect()->route('getScheduledItemsUrl');
    }

    public function items(UserService $userService) {
        $user = $userService->getCurrentUser();

        if ($user === null || $user->rights < User::RIGHTS_EXTENDED) {
            return response(view('errors/404'), 404);
        }

        return view('scheduled/items', array(
            'items' => $user->scheduled
        ));
    }

    public function delete(Request $request, UserService $userService, $id) {
        $user = $userService->getCurrentUser();

        if ($user === null || $user->rights < User::RIGHTS_EXTENDED) {
            return response(view('errors/404'), 404);
        }

        $entity = ScheduledPost::where('id', '=', $id)->first();

        if ($entity === null) {
            return response(view('errors/404'), 404);
        }

        if ($entity->user_id !== $user->id
                && $user->rights != 99) {
            return response(view('errors/404'), 404);
        }

        $log = new Log();

        $log->user_id = $user->id;
        $log->type = Log::TYPE_SCHEDULED_DELETED;
        $log->scheduled_post_id = $entity->id;

        $log->save();

        $entity->deleted_by = $user->id;
        $entity->save();

        $entity->delete();

        $request->session()->flash('flashSuccess', 'Post usunięty');

        return redirect()->back();
    }
}