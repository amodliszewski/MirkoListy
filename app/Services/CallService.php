<?php
namespace App\Services;

use WykoCommon\Services\CallService as Base;
use App\Models\User;

class CallService extends Base
{
    public function singleCallFromEntry($sourceEntry, $sourceComment, $entryId, $linkId, $type, $perComment) {
        $users = [];

        $firstCommentPrefix = 'Wołam przez [MirkoListy](https://mirkolisty.pvu.pl) ';
        if ($type === 1) {
            if ($sourceComment !== null) {
                $firstCommentPrefix .= "plusujących [ten komentarz](http://wykop.pl/wpis/" . $sourceEntry['entry_id'] . "/#comment-" . $sourceComment['id'] . ")";

                foreach ($sourceComment['voters'] as $user) {
                    $users[] = $user['author'];
                }
            } else {
                $firstCommentPrefix .= "plusujących [ten wpis](http://wykop.pl/wpis/" . $sourceEntry['entry_id'] . ")";

                foreach ($sourceEntry['voters'] as $user) {
                    $users[] = $user['author'];
                }
            }
        } else if ($type === 2) {
            $firstCommentPrefix .= "komentujących [ten wpis](http://wykop.pl/wpis/" . $sourceEntry['entry_id'] . ")";

            foreach ($sourceEntry['comments'] as $user) {
                $users[] = $user['author'];
            }
        } else if ($type === 3) {
            if ($sourceComment !== null) {
                $firstCommentPrefix .= "plusujących [ten komentarz](http://wykop.pl/wpis/" . $sourceEntry['entry_id'] . "/#comment-" . $sourceComment['id'] . ") i komentujących [ten wpis](http://wykop.pl/wpis/" . $sourceEntry['entry_id'] . ")";

                foreach ($sourceComment['voters'] as $user) {
                    $users[] = $user['author'];
                }
            } else {
                $firstCommentPrefix .= "plusujących i komentujących [ten wpis](http://wykop.pl/wpis/" . $sourceEntry['entry_id'] . ")";

                foreach ($sourceEntry['voters'] as $user) {
                    $users[] = $user['author'];
                }
            }

            foreach ($sourceEntry['comments'] as $user) {
                $users[] = $user['author'];
            }
        }

        $this->singleCall($entryId, $linkId, $firstCommentPrefix, $users, $perComment);
    }

    public function singleCallCustom($entryId, $linkId, $firstCommentPrefix, $users, $perComment) {
        $this->singleCall($entryId, $linkId, $firstCommentPrefix, $users, $perComment);
    }

    public function singleCall($entryId, $linkId, $firstCommentPrefix, $users, $perComment) {
        $users = array_unique($users);

        $optOutUsers = User::whereIn('nick', $users)->where('call_optout', 1)->get();

        if (!empty($optOutUsers)) {
            $optOutUsersArray = [];

            foreach ($optOutUsers as $loopItem) {
                $optOutUsersArray[] = $loopItem->nick;
            }

            $users = array_diff($users, $optOutUsersArray);
        }

        if (empty($users)) {
            return;
        }

        $preparedUsers = [];

        foreach ($users as $user) {
            if (strpos($user, '..') !== false) {
                continue;
            }

            $preparedUsers[] = $_ENV['WYKOP_CALL_PREFIX'] . $user;
        }

        $firstCommentPrefix .= ' (' . count($preparedUsers) . ")\n\n";
        $firstCommentPrefix .= "Dodatek wspierany przez [**Cebula.Online**](https://cebula.online/?utm_source=social&utm_medium=wykop&utm_campaign=mirkolisty)\n\n";
        $firstCommentPrefix .= "Nie chcesz być wołany/a jako plusujący/a? Włącz blokadę na https://mirkolisty.pvu.pl/call lub odezwij się do @[IrvinTalvanen](http://www.wykop.pl/ludzie/IrvinTalvanen/)";
        $firstCommentPrefix .= "\n\nUważasz, że wołający nadużywa MirkoList? Daj znać @[IrvinTalvanen](http://www.wykop.pl/ludzie/IrvinTalvanen/)\n\n";

        $this->saveQueue($firstCommentPrefix, $preparedUsers, $entryId, $linkId, $perComment);
    }

    protected function queueComments($spamlists, $entryId, $linkId, $users, $perComment) {
        $spamlistsNames = array();
        foreach ($spamlists as $spamlist) {
            $spamlistsNames[] = '[' . str_replace('#', '', $spamlist->name) . '](https://mirkolisty.pvu.pl/list/' . $spamlist->uid . ')';
        }

        $firstCommentPrefix = 'Wołam zainteresowanych (' . count($users) . ') z ';
        if (count($spamlistsNames) === 1) {
            $firstCommentPrefix .= 'listy ';
        } else {
            $firstCommentPrefix .= 'list ';
        }

        $firstCommentPrefix .= implode(', ', $spamlistsNames) . "\n";

        $firstCommentPrefix .= "**Możesz zapisać/wypisać się klikając na nazwę listy.**\n\n";
        $firstCommentPrefix .= "Dodatek wspierany przez [**Cebula.Online**](https://cebula.online/?utm_source=social&utm_medium=wykop&utm_campaign=mirkolisty)\n\n";
        $firstCommentPrefix .= "Masz problem z działaniem listy? A może pytanie? Pisz do [IrvinTalvanen](http://www.wykop.pl/ludzie/IrvinTalvanen/)\n\n";

        $prepared = array();

        foreach ($users as $user) {
            $prepared[] = $_ENV['WYKOP_CALL_PREFIX'] . $user;
        }

        if (count($prepared) > 0) {
            $this->saveQueue($firstCommentPrefix, $prepared, $entryId, $linkId, $perComment);
        }
    }

    public function getInternalNonListLimit($group) {
        switch ($group) {
            case 0:
                return 10;
            case 2:
                return 50;
            case 1001:
            case 1002:
                return 0;
            case 1:
            case 5:
            case 2001:
            default:
                return 20;
        }
    }
}
