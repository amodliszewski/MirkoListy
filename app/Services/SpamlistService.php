<?php
namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use App\Models\Spamlist;
use App\Models\Call;
use App\Models\Log;
use App\Models\UserSpamlist;
use App\Services\CallService;
use WykoCommon\Services\WykopService;

class SpamlistService
{
    private $request = null;
    private $callService;
    private $wykopService;

    public function __construct(
        Request $request,
        CallService $callService,
        WykopService $wykopService
    ) {
        $this->request = $request;
        $this->callService = $callService;
        $this->wykopService = $wykopService;
    }

    public function call($user, $entryUrl, $selectedSex, $spamlists, $perComment) {
        if ($perComment === 0) {
            return 'Twoje konto nie ma uprawnień do wołania.';
        }

        $entryId = $this->wykopService->getEntryId($entryUrl);
        
        if ($entryId === null) {
            return 'Niepoprawny adres wpisu';
        }

        $entryData = $this->wykopService->getEntryData($entryId);

        if ($entryData === null) {
            return 'Nie udało się pobrać danych wpisu';
        }

        if (Cache::has('call_lock_' . $user->id)) {
            $latestCallDate = new \DateTime(Cache::get('call_lock_' . $user->id));

            return 'Musisz poczekać <span class="countdownTimer">' . $latestCallDate->format('Y-m-d H:i:s') . '</span> min zanim zawołasz ponownie.';
        }

        $callDate = new \DateTime('+' . $_ENV['CALLS_DELAY_MINUTES'] . ' minutes');

        Cache::put('call_lock_' . $user->id, $callDate->format('Y-m-d H:i:s'), $_ENV['CALLS_DELAY_MINUTES']);

        $entities = array();
        $users = array();
        foreach ($spamlists as $spamlist) {
            $spamlist = preg_replace('/[^0-9A-Za-z]/', '', $spamlist);

            $entity = Spamlist::where('uid', '=', $spamlist)->first();

            if ($entity === null) {
                return 'Lista ' . $spamlist . ' nie istnieje';
            }

            if (!$this->checkRights($entity, $user, UserSpamlist::ACTION_CALL)) {
                return 'Nie masz odpowiednich uprawnień do listy wołania listy ' . $entity->name;
            }

            $pivotEntities = UserSpamlist::where('spamlist_id', '=', $entity['id'])
                    ->where('rights', '!=', 2)
                    ->get();

            if ($pivotEntities->count() === 0) {
                continue;
            }

            if (!in_array($entity->user->nick, $users)) {
                $users[] = $entity->user->nick;
            }

            foreach ($pivotEntities as $pivotEntity) {
                if ($selectedSex !== 0 && $pivotEntity->user->sex !== $selectedSex) {
                    continue;
                }

                if (!in_array($pivotEntity->user->nick, $users)) {
                    $users[] = $pivotEntity->user->nick;
                }
            }

            $call = Call::where('entry_id', '=', $entryData['entry_id'])
                    ->where('spamlist_id', '=', $entity['id'])
                    ->first();

            if ($call === null) {
                $call = new Call();

                $call->user_id = $user['id'];
                $call->spamlist_id = $entity['id'];
                $call->entry_id = $entryData['entry_id'];
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

            $entities[] = $entity;
        }

        $user->called_count++;
        $user->timestamps = false;

        $user->save();

        $this->callService->call($entities, $entryId, null, $users, $perComment);

        return true;
    }

    public function getUserCreatedSpamlists($userId = null) {
        if ($userId === null) {
            $userId = Session::get('userId');
        }

        if ($userId === null) {
            return array();
        }

        $entities = Spamlist::where('user_id', '=', $userId)->get();

        $items = array();

        if ($entities->count() > 0) {
            foreach ($entities as $entity) {
                $items[] = array(
                    'uid' => $entity['uid'],
                    'name' => $entity['name'],
                    'created_at' => $entity['created_at'],
                    'creator' => null,
                    'description' => $entity['description'],
                    'joined_count' => $entity['joined_count'],
                    'called_count' => $entity['called_count']
                );
            }
        }

        return $items;
    }

    public function getUserJoinedSpamlists($userId = null) {
        if ($userId === null) {
            $userId = Session::get('userId');
        }

        if ($userId === null) {
            return array();
        }

        $entities = UserSpamlist::where('user_id', '=', $userId)->get();

        $items = array();

        if ($entities->count() > 0) {
            foreach ($entities as $entity) {
                $spamlist = $entity->spamlist;

                if ($spamlist === null) {
                    continue;
                }

                $items[] = array(
                    'uid' => $spamlist->uid,
                    'name' => $spamlist->name,
                    'creator' => $spamlist->user->nick,
                    'description' => $spamlist->description,
                    'joined_count' => $spamlist->joined_count,
                    'called_count' => $spamlist->called_count,
                    'joined_at' => $entity->created_at,
                    'rights' => $entity->rights
                );
            }
        }

        return $items;
    }

    public function getUserCallableSpamlists($userId = null) {
        if ($userId === null) {
            $userId = Session::get('userId');
        }

        if ($userId === null) {
            return array();
        }

        $entities = UserSpamlist::where('user_id', '=', $userId)
                ->where('rights', '>=', UserSpamlist::RIGHTS_EXTENDED)
                ->get();

        $items = $this->getUserCreatedSpamlists($userId);

        if ($entities->count() > 0) {
            foreach ($entities as $entity) {
                $spamlist = $entity->spamlist;

                if ($spamlist === null) {
                    continue;
                }

                $items[] = array(
                    'uid' => $spamlist->uid,
                    'name' => $spamlist->name,
                    'creator' => $spamlist->user->nick,
                    'description' => $spamlist->description,
                    'joined_count' => $spamlist->joined_count,
                    'called_count' => $spamlist->called_count
                );
            }
        }

        return $items;
    }

    public function getUserSpamlistRightsText($rights) {
        switch ($rights) {
            case UserSpamlist::RIGHTS_BANNED:
                return 'zbanowany';
            case UserSpamlist::RIGHTS_DEFAULT:
                return 'użytkownik';
            case UserSpamlist::RIGHTS_EXTENDED:
                return 'wołający';
            case UserSpamlist::RIGHTS_ADMIN:
                return 'administrator';
            default:
                return 'nieznane uprawnienie';
        }
    }

    public function checkRights($spamlist, $user, $action) {
        if ($spamlist === null || $user === null) {
            return false;
        }

        if ($spamlist->user_id === $user->id) {
            return true;
        }

        if ($user->rights == 99) {
            return true;
        }

        $pivot = UserSpamlist::where('user_id', '=', $user->id)
                ->where('spamlist_id', '=', $spamlist->id)
                ->first();

        if ($pivot === null) {
            return false;
        }

        switch ($action) {
            case UserSpamlist::ACTION_CHANGE_RIGHTS:
            case UserSpamlist::ACTION_EDIT:
                return $pivot->rights >= UserSpamlist::RIGHTS_ADMIN;
            case UserSpamlist::ACTION_CALL:
                return $pivot->rights >= UserSpamlist::RIGHTS_EXTENDED;
            default:
                return false;
        }
    }
}