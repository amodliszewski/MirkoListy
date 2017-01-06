<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Validator;
use App\Services\CallService;
use WykoCommon\Services\UserService;
use WykoCommon\Services\WykopService;
use WykoCommon\Models\Queue;
use App\Models\Log;
use App\Models\User;
use App\Models\UserSpamlist;

class CallController extends Controller
{
    public function optout(Request $request,
            UserService $userService) {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:0,1'
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $request->session()->flash('flashError', implode("<br />", $messages));

            return redirect()->back()->withInput();
        }

        $user = $userService->getCurrentUser();

        if ($user === null) {
            return response(view('errors/404'), 404);
        }

        $log = new Log();

        $log->user_id = $user->id;

        if ($request->get('type') == 1) {
            $log->type = Log::TYPE_RIGHTS_CALL_OPTOUT_ON;

            $request->session()->flash('flashSuccess', 'Blokada włączona');
        } else {
            $log->type = Log::TYPE_RIGHTS_CALL_OPTOUT_OFF;

            $request->session()->flash('flashSuccess', 'Blokada wyłączona');
        }

        $log->save();

        Cache::forget('user_' . $user->id);

        $user->call_optout = (int) $request->get('type');

        $user->save();

        return redirect()->back();
    }

    public function call(Request $request,
            UserService $userService,
            WykopService $wykopService,
            CallService $callService) {
        if (isset($_ENV['IS_OFFLINE']) && $_ENV['IS_OFFLINE'] == 1) {
            return redirect(route('offline'));
        }

        $validator = Validator::make($request->all(), [
            'entryUrl' => 'required|URL',
            'type' => 'required|in:1,2,3,10,11,12,13,20'
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $request->session()->flash('flashError', implode("<br />", $messages));

            return redirect()->back()->withInput();
        }

        $user = $userService->getCurrentUser();

        if ($user === null) {
            return response(view('errors/404'), 404);
        }

        if (Cache::has('call_lock_' . $user->id)) {
            $latestCallDate = new \DateTime(Cache::get('call_lock_' . $user->id));

            $request->session()->flash('flashError', 'Musisz poczekać <span class="countdownTimer">' . $latestCallDate->format('Y-m-d H:i:s') . '</span> min zanim zawołasz ponownie.');

            return redirect()->back()->withInput();
        }

        $currentQueue = Queue::where('user_id', '=', $user->id)
                ->whereNotNull('user_key')
                ->first();

        if ($currentQueue !== null) {
            $request->session()->flash('flashError', 'Twoje poprzednie wołanie jest nadal w trakcie. Musisz poczekać aż zostanie zakończone (może to potrwać do 15 minut).');

            return redirect()->back()->withInput();
        }

        $type = (int) $request->get('type');

        $entryId = $wykopService->getEntryId($request->get('entryUrl'));
        $linkId = $wykopService->getLinkId($request->get('entryUrl'));

        if ($entryId === null && $linkId === null) {
            $request->session()->flash('flashError', 'Niepoprawny adres wpisu/znaleziska');

            return redirect()->back()->withInput();
        }

        if ($entryId !== null) {
            $entryData = $wykopService->getEntryData($entryId);

            if ($entryData === null) {
                $request->session()->flash('flashError', 'Nie udało się pobrać danych wpisu');

                return redirect()->back()->withInput();
            }

            if (stripos($entryData['content'], 'rozdajo') !== false && $user->rights < User::RIGHTS_EXTENDED) {
                $request->session()->flash('flashError', 'Wołanie do wpisów z #rozdajo jest zakazane!');

                return redirect()->back()->withInput();
            }
        } else {
            $linkData = $wykopService->getLinkData($linkId);

            if ($linkData === null) {
                $request->session()->flash('flashError', 'Nie udało się pobrać danych znaleziska');

                return redirect()->back()->withInput();
            }
        }

        $perComment = $callService->getGroupPerComment($request->session()->get('wykopGroup'));

        if ($perComment === 0) {
            $request->session()->flash('flashError', 'Twoje konto nie ma uprawnień do wołania.');

            return redirect()->back()->withInput();
        }

        if (in_array($type, array(1, 2, 3))) {
            $validator = Validator::make($request->all(), [
                'sourceEntryUrl' => 'required|URL'
            ]);

            if ($validator->fails()) {
                $messages = $validator->errors()->all();
                $request->session()->flash('flashError', implode("<br />", $messages));

                return redirect()->back()->withInput();
            }

            $sourceEntryId = $wykopService->getEntryId($request->get('sourceEntryUrl'));
            $sourceCommentId = $wykopService->getCommentId($request->get('sourceEntryUrl'));

            if ($sourceEntryId === null) {
                $request->session()->flash('flashError', 'Niepoprawny adres wpisu źrodłowego');

                return redirect()->back()->withInput();
            }

            $sourceEntryData = $wykopService->getEntryData($sourceEntryId);

            if ($sourceEntryData === null) {
                $request->session()->flash('flashError', 'Nie udało się pobrać danych wpisu źrodłowego');

                return redirect()->back()->withInput();
            }

            if ($sourceEntryData['author'] !== $user->nick && $user->rights < User::RIGHTS_EXTENDED) {
                $request->session()->flash('flashError', 'Wpis źrodłowy nie należy do Ciebie!');

                return redirect()->back()->withInput();
            }

            $sourceComment = null;
            if (($type === 1 || $type === 3) && $sourceCommentId > 0) {
                foreach ($sourceEntryData['comments'] as $comment) {
                    if ($comment['id'] === $sourceCommentId) {
                        $sourceComment = $comment;

                        break;
                    }
                }

                if ($sourceComment === null) {
                    $request->session()->flash('flashError', 'Nieprawidłowy link źrodłowy. Nie znalazłem szukanego komentarza!');

                    return redirect()->back()->withInput();
                }
            }

            $log = new Log();

            $log->user_id = $user->id;
            if ($entryId !== null) {
                if ($type === 1) {
                    if ($sourceComment !== null) {
                        $log->type = Log::TYPE_SINGLE_CALL_VOTERS_COMMENT;
                    } else {
                        $log->type = Log::TYPE_SINGLE_CALL_VOTERS;
                    }
                } else if ($type === 3) {
                    if ($sourceComment !== null) {
                        $log->type = Log::TYPE_SINGLE_CALL_VOTERS_AND_COMMENTERS_COMMENT;
                    } else {
                        $log->type = Log::TYPE_SINGLE_CALL_VOTERS_AND_COMMENTERS;
                    }
                } else {
                    $log->type = Log::TYPE_SINGLE_CALL_COMMENTERS;
                }

                $log->single_entry = $entryData['entry_id'];
            } else {
                if ($type === 1) {
                    if ($sourceComment !== null) {
                        $log->type = Log::TYPE_SINGLE_LINK_CALL_VOTERS_COMMENT;
                    } else {
                        $log->type = Log::TYPE_SINGLE_LINK_CALL_VOTERS;
                    }
                } else if ($type === 3) {
                    if ($sourceComment !== null) {
                        $log->type = Log::TYPE_SINGLE_LINK_CALL_VOTERS_AND_COMMENTERS_COMMENT;
                    } else {
                        $log->type = Log::TYPE_SINGLE_LINK_CALL_VOTERS_AND_COMMENTERS;
                    }
                } else {
                    $log->type = Log::TYPE_SINGLE_LINK_CALL_COMMENTERS;
                }

                $log->single_entry = $linkData['link_id'];
            }

            $log->single_source_entry = $sourceEntryData['entry_id'];
            if ($sourceComment !== null) {
                $log->single_source_comment = $sourceComment['id'];
            }

            $log->save();

            $callService->singleCallFromEntry($sourceEntryData, $sourceComment, $entryId, $linkId, $type, $perComment);
        } else if (in_array($type, array(10, 11, 12, 13))) {
            $validator = Validator::make($request->all(), [
                'sourceEntryUrl' => 'required|URL'
            ]);

            if ($validator->fails()) {
                $messages = $validator->errors()->all();
                $request->session()->flash('flashError', implode("<br />", $messages));

                return redirect()->back()->withInput();
            }

            $sourceLinkId = $wykopService->getLinkId($request->get('sourceEntryUrl'));

            if ($sourceLinkId === null) {
                $request->session()->flash('flashError', 'Niepoprawny adres linku źrodłowego');

                return redirect()->back()->withInput();
            }

            $sourceLinkData = $wykopService->getLinkData($sourceLinkId);

            if ($sourceLinkData === null) {
                $request->session()->flash('flashError', 'Nie udało się pobrać danych linku źrodłowego');

                return redirect()->back()->withInput();
            }

            if ($sourceLinkData['author'] !== $user->nick && $user->rights < User::RIGHTS_EXTENDED) {
                $request->session()->flash('flashError', 'Link źrodłowy nie należy do Ciebie!');

                return redirect()->back()->withInput();
            }

            $users = [];

            if ($type === 10 || $type == 13) {
                $tmpUsers = $wykopService->getLinkDigs($sourceLinkId);

                foreach ($tmpUsers as $loopItem) {
                    $users[] = $loopItem['author'];
                }
            }

            if ($type === 11 || $type == 13) {
                $tmpUsers = $wykopService->getLinkBuries($sourceLinkId);

                foreach ($tmpUsers as $loopItem) {
                    $users[] = $loopItem['author'];
                }
            }

            if ($type === 12 || $type == 13) {
                $tmpUsers = $wykopService->getLinkComments($sourceLinkId);

                foreach ($tmpUsers as $loopItem) {
                    $users[] = $loopItem['author'];
                }
            }

            $log = new Log();

            $log->user_id = $user->id;

            if ($entryId != null) {
                $log->single_entry = $entryId;

                if ($type === 10) {
                    $log->type = Log::TYPE_SINGLE_CALL_LINK_DIGS;

                    $firstCommentPrefix = 'Wołam przez [MirkoListy](http://mirkolisty.pvu.pl) wykopujących [to znalezisko](http://wykop.pl/link/' . $sourceLinkId . ')';
                } else if ($type === 11) {
                    $log->type = Log::TYPE_SINGLE_CALL_LINK_BURIES;

                    $firstCommentPrefix = 'Wołam przez [MirkoListy](http://mirkolisty.pvu.pl) zakopujących [to znalezisko](http://wykop.pl/link/' . $sourceLinkId . ')';
                } else if ($type === 12) {
                    $log->type = Log::TYPE_SINGLE_CALL_LINK_COMMENTERS;

                    $firstCommentPrefix = 'Wołam przez [MirkoListy](http://mirkolisty.pvu.pl) komentujących [to znalezisko](http://wykop.pl/link/' . $sourceLinkId . ')';
                } else if ($type === 13) {
                    $log->type = Log::TYPE_SINGLE_CALL_LINK_ALL;

                    $firstCommentPrefix = 'Wołam przez [MirkoListy](http://mirkolisty.pvu.pl) aktywnych pod [tym znaleziskiem](http://wykop.pl/link/' . $sourceLinkId . ')';
                }
            } else {
                $log->single_entry = $linkId;

                if ($type === 10) {
                    $log->type = Log::TYPE_SINGLE_LINK_CALL_LINK_DIGS;

                    $firstCommentPrefix = 'Wołam przez [MirkoListy](http://mirkolisty.pvu.pl) wykopujących [to znalezisko](http://wykop.pl/link/' . $sourceLinkId . ')';
                } else if ($type === 11) {
                    $log->type = Log::TYPE_SINGLE_LINK_CALL_LINK_BURIES;

                    $firstCommentPrefix = 'Wołam przez [MirkoListy](http://mirkolisty.pvu.pl) zakopujących [to znalezisko](http://wykop.pl/link/' . $sourceLinkId . ')';
                } else if ($type === 12) {
                    $log->type = Log::TYPE_SINGLE_LINK_CALL_LINK_COMMENTERS;

                    $firstCommentPrefix = 'Wołam przez [MirkoListy](http://mirkolisty.pvu.pl) komentujących [to znalezisko](http://wykop.pl/link/' . $sourceLinkId . ')';
                } else if ($type === 13) {
                    $log->type = Log::TYPE_SINGLE_LINK_CALL_LINK_ALL;

                    $firstCommentPrefix = 'Wołam przez [MirkoListy](http://mirkolisty.pvu.pl) aktywnych pod [tym znaleziskiem](http://wykop.pl/link/' . $sourceLinkId . ')';
                }
            }

            $log->single_source_entry = $sourceLinkId;

            $log->save();

            $callService->singleCallCustom($entryId, $linkId, $firstCommentPrefix, $users, $perComment);
        } else if ($type === 20) {
            if (!$userService->isAdmin()) {
                $request->session()->flash('flashError', 'Niestety nie masz odpowiednich uprawnień.');

                return redirect()->back()->withInput();
            }

            $users = [];

            $owners = User::where('created_count', '>', 0)->get();

            foreach ($owners as $loopItem) {
                $users[] = $loopItem->nick;
            }

            $callers = UserSpamlist::where('rights', '>=', 20)->get();

            foreach ($callers as $loopItem) {
                $users[] = $loopItem->user->nick;
            }

            $log = new Log();

            $log->user_id = $user->id;

            if ($entryId != null) {
                $log->single_entry = $entryId;

                $log->type = Log::TYPE_SINGLE_CALL_OWNERS;
            } else {
                $log->single_entry = $linkId;

                $log->type = Log::TYPE_LINK_SINGLE_CALL_OWNERS;
            }

            $log->save();

            $firstCommentPrefix = 'Wołam przez [MirkoListy](http://mirkolisty.pvu.pl) właścicieli spamlist i wołających';

            $callService->singleCallCustom($entryId, $linkId, $firstCommentPrefix, $users, $perComment);
        }

        $callDate = new \DateTime('+' . $_ENV['CALLS_DELAY_MINUTES'] . ' minutes');

        Cache::put('call_lock_' . $user->id, $callDate->format('Y-m-d H:i:s'), $_ENV['CALLS_DELAY_MINUTES']);

        $request->session()->flash('flashSuccess', 'Wołanie dodane do kolejki');

        return redirect()->back();
    }
}