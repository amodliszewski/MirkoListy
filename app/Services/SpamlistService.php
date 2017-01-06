<?php
namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Spamlist;
use App\Models\UserSpamlist;

class SpamlistService
{
    private $request = null;

    public function __construct(Request $request) {
        $this->request = $request;
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