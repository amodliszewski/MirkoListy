<?php
namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Spamlist;
use App\Services\SpamlistService;
use App\Models\UserSpamlist;
use App\Models\User;
use App\Models\Log;
use App\Models\Call;
use WykoCommon\Services\WykopService;

class APIController extends Controller
{
    public function userCallable(SpamlistService $spamlistService, $userNick) {
        $user = User::where('nick', '=', $userNick)->first();

        if ($user === null) {
            return response()->json([
                'items' => []
            ]);
        }

        $items = $spamlistService->getUserCallableSpamlists($user->id);

        $preparedItems = [];

        if (is_array($items) && !empty($items)) {
            foreach ($items as $loopItem) {
                $preparedItems[$loopItem['uid']] = [
                    'uid' => $loopItem['uid'],
                    'name' => $loopItem['name'],
                    'creator' => $loopItem['creator'] === null ? $user->nick : $loopItem['creator']
                ];
            }
        }

        return response()->json([
            'items' => $preparedItems
        ]);
    }

    public function usersOnSpamlist($uid) {
        $entity = Spamlist::where('uid', '=', $uid)->first();

        if ($entity === null) {
            return response()->json([
                'error' => 'Incorrect request'
            ], 400);
        }

        $items = UserSpamlist::select(DB::raw('user_spamlists.*, users.nick, users.id as user_id'))
                ->join('users', 'users.id', '=', 'user_spamlists.user_id')
                ->where('spamlist_id', '=', $entity->id)
                ->orderBy('user_spamlists.rights', 'desc')
                ->orderBy('users.nick', 'asc')
                ->get();

        $preparedItems = [];

        if (is_object($items) && $items->count() > 0) {
            foreach ($items as $loopItem) {
                $preparedItems[] = [
                    'nick' => $loopItem['nick']
                ];
            }
        }

        return response()->json([
            'items' => $preparedItems
        ]);
    }

    public function confirmCall(Request $request, WykopService $wykopService, $uid) {
        if (!$request->hasHeader('Auth-Key') || $request->header('Auth-Key') !== $_ENV['API_MIRKOLISTY_KEY']) {
            return response()->json([
                'error' => 'Incorrect request'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'userNick' => 'required|exists:users,nick',
            'entryId' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Incorrect request'
            ], 400);
        }

        $entity = Spamlist::where('uid', '=', $uid)->first();

        if ($entity === null) {
            return response()->json([
                'error' => 'Incorrect request'
            ], 400);
        }

        $user = User::where('nick', '=', $request->get('userNick'))->first();

        if ($user === null) {
            return response()->json([
                'error' => 'Incorrect request'
            ], 400);
        }

        $entryData = $wykopService->getEntryData($request->get('entryId'));

        if ($entryData === null) {
            return response()->json([
                'error' => 'Incorrect entryId'
            ], 400);
        }

        $call = Call::where('entry_id', '=', $request->get('entryId'))
            ->where('spamlist_id', '=', $entity->id)
            ->first();

        if ($call === null) {
            $call = new Call();

            $call->user_id = $user->id;
            $call->spamlist_id = $entity->id;
            $call->entry_id = $request->get('entryId');
        }

        $call->author = $entryData['author'];
        $call->author_avatar = $entryData['author_avatar'];
        $call->author_sex = $entryData['author_sex'];
        $call->author_group = $entryData['author_group'];
        $call->posted_at = $entryData['posted_at'];
        $call->content = $entryData['content'];
        $call->image_url = $entryData['image_url'];
        $call->big_image_url = $entryData['big_image_url'];

        $call->save();

        $log = new Log();

        $log->user_id = $user->id;
        $log->type = Log::TYPE_CALL;
        $log->spamlist_id = $entity->id;
        $log->call_id = $call->id;

        $log->save();

        $entity->called_count++;
        $entity->last_called_at = date('Y-m-d H:i:s');
        $entity->timestamps = false;

        $entity->save();

        $user->called_count++;
        $user->timestamps = false;

        $user->save();

        return response()->json([
            'message' => 'Confirmed'
        ]);
    }
}