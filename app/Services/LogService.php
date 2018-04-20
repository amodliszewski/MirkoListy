<?php
namespace App\Services;

use WykoCommon\Services\LogService as Base;
use App\Models\Log;

class LogService extends Base
{
    protected function getMaleLogText($log) {
        switch ($log->type) {
            case Log::TYPE_CALL:
                if ($log->call->link_id !== null) {
                    return $this->templateService->getUserProfileUrl($log->user)
                        . ' zawołał do <a href="' . $_ENV['WYKOP_BASE_URL'] . 'link/' . $log->call->link_id . '" rel="nofollow">znaleziska</a>';
                } else {
                    return $this->templateService->getUserProfileUrl($log->user)
                        . ' zawołał do <a href="' . $_ENV['WYKOP_BASE_URL'] . 'wpis/' . $log->call->entry_id . '" rel="nofollow">wpisu</a>';
                }

            case Log::TYPE_JOINED_SELF:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' dołączył do listy';

            case Log::TYPE_JOINED_ADMIN:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' dodał do listy użytkownika <strong>' . $this->templateService->getUserProfileUrl($log->subject) . '</strong>';

            case Log::TYPE_LEFT_SELF:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' opuścił listę';

            case Log::TYPE_LEFT_ADMIN:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' usunął użytkownika <strong>' . $this->templateService->getUserProfileUrl($log->subject) . '</strong> z listy';

            case Log::TYPE_RIGHTS_CHANGE:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zmienił uprawnienia użytkownika ' . $this->templateService->getUserProfileUrl($log->subject);

            case Log::TYPE_RIGHTS_BANNED:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zbanował użytkownika ' . $this->templateService->getUserProfileUrl($log->subject);

            case Log::TYPE_RIGHTS_CALL_OPTOUT_ON:
                if (!empty($log->subject)) {
                    return $this->templateService->getUserProfileUrl($log->user)
                        . ' włączył blokadę wołania użytkownika ' . $this->templateService->getUserProfileUrl($log->subject);
                } else {
                    return $this->templateService->getUserProfileUrl($log->user)
                        . ' włączył blokadę wołania';
                }

            case Log::TYPE_RIGHTS_CALL_OPTOUT_OFF:
                if (!empty($log->subject)) {
                    return $this->templateService->getUserProfileUrl($log->user)
                        . ' wyłączył blokadę wołania użytkownika ' . $this->templateService->getUserProfileUrl($log->subject);
                } else {
                    return $this->templateService->getUserProfileUrl($log->user)
                        . ' wyłączył blokadę wołania';
                }

            case Log::TYPE_CREATED:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' utworzył listę';

            case Log::TYPE_DELETED:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' usunął listę';

            case Log::TYPE_EDITED:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' edytował listę';

            case Log::TYPE_SINGLE_CALL_VOTERS:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zawołał plusujących <a rel="nofollow" href="' . $_ENV['WYKOP_BASE_URL'] . 'wpis/' . $log->single_source_entry . '">ten wpis</a> do <a rel="nofollow" href="http://wykop.pl/wpis/' . $log->single_entry . '">tego wpisu</a>';

            case Log::TYPE_SINGLE_CALL_VOTERS_COMMENT:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zawołał plusujących <a rel="nofollow" href="' . $_ENV['WYKOP_BASE_URL'] . 'wpis/' . $log->single_source_entry . '/#comment-' . $log->single_source_comment . '">ten komentarz</a> do <a rel="nofollow" href="http://wykop.pl/wpis/' . $log->single_entry . '">tego wpisu</a>';

            case Log::TYPE_SINGLE_CALL_COMMENTERS:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zawołał komentujących <a rel="nofollow" href="' . $_ENV['WYKOP_BASE_URL'] . 'wpis/' . $log->single_source_entry . '">ten wpis</a> do <a rel="nofollow" href="http://wykop.pl/wpis/' . $log->single_entry . '">tego wpisu</a>';

            case Log::TYPE_SINGLE_CALL_VOTERS_AND_COMMENTERS:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zawołał plusujących i komentujących <a rel="nofollow" href="' . $_ENV['WYKOP_BASE_URL'] . 'wpis/' . $log->single_source_entry . '">ten wpis</a> do <a rel="nofollow" href="http://wykop.pl/wpis/' . $log->single_entry . '">tego wpisu</a>';

            case Log::TYPE_SINGLE_CALL_VOTERS_AND_COMMENTERS_COMMENT:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zawołał plusujących <a rel="nofollow" href="' . $_ENV['WYKOP_BASE_URL'] . 'wpis/' . $log->single_source_entry . '/#comment-' . $log->single_source_comment . '">ten komentarz</a> i komentujących <a rel="nofollow" href="http://wykop.pl/wpis/' . $log->single_source_entry . '">ten wpis</a> do <a rel="nofollow" href="http://wykop.pl/wpis/' . $log->single_entry . '">tego wpisu</a>';

            case Log::TYPE_SINGLE_CALL_OWNERS:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zawołał właścicieli spamlist do <a rel="nofollow" href="' . $_ENV['WYKOP_BASE_URL'] . 'wpis/' . $log->single_entry . '">tego wpisu</a>';

            case Log::TYPE_SINGLE_CALL_LINK_DIGS:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zawołał wykopujących <a rel="nofollow" href="' . $_ENV['WYKOP_BASE_URL'] . 'link/' . $log->single_source_entry . '">to znalezisko</a> do <a rel="nofollow" href="http://wykop.pl/wpis/' . $log->single_entry . '">tego wpisu</a>';

            case Log::TYPE_SINGLE_CALL_LINK_BURIES:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zawołał zakopujących <a rel="nofollow" href="' . $_ENV['WYKOP_BASE_URL'] . 'link/' . $log->single_source_entry . '">to znalezisko</a> do <a rel="nofollow" href="http://wykop.pl/wpis/' . $log->single_entry . '">tego wpisu</a>';

            case Log::TYPE_SINGLE_CALL_LINK_COMMENTERS:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zawołał komentujących <a rel="nofollow" href="' . $_ENV['WYKOP_BASE_URL'] . 'link/' . $log->single_source_entry . '">to znalezisko</a> do <a rel="nofollow" href="http://wykop.pl/wpis/' . $log->single_entry . '">tego wpisu</a>';

            case Log::TYPE_SINGLE_CALL_LINK_ALL:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zawołał aktywnych w <a rel="nofollow" href="' . $_ENV['WYKOP_BASE_URL'] . 'link/' . $log->single_source_entry . '">tym znalezisku</a> do <a rel="nofollow" href="http://wykop.pl/wpis/' . $log->single_entry . '">tego wpisu</a>';

            case Log::TYPE_SINGLE_LINK_CALL_VOTERS:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zawołał plusujących <a rel="nofollow" href="' . $_ENV['WYKOP_BASE_URL'] . 'wpis/' . $log->single_source_entry . '">ten wpis</a> do <a rel="nofollow" href="http://wykop.pl/link/' . $log->single_entry . '">tego znaleziska</a>';

            case Log::TYPE_SINGLE_LINK_CALL_VOTERS_COMMENT:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zawołał plusujących <a rel="nofollow" href="' . $_ENV['WYKOP_BASE_URL'] . 'wpis/' . $log->single_source_entry . '/#comment-' . $log->single_source_comment . '">ten komentarz</a> do <a rel="nofollow" href="http://wykop.pl/link/' . $log->single_entry . '">tego znaleziska</a>';

            case Log::TYPE_SINGLE_LINK_CALL_COMMENTERS:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zawołał komentujących <a rel="nofollow" href="' . $_ENV['WYKOP_BASE_URL'] . 'wpis/' . $log->single_source_entry . '">ten wpis</a> do <a rel="nofollow" href="http://wykop.pl/link/' . $log->single_entry . '">tego znaleziska</a>';

            case Log::TYPE_SINGLE_LINK_CALL_VOTERS_AND_COMMENTERS:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zawołał plusujących i komentujących <a rel="nofollow" href="' . $_ENV['WYKOP_BASE_URL'] . 'wpis/' . $log->single_source_entry . '">ten wpis</a> do <a rel="nofollow" href="http://wykop.pl/link/' . $log->single_entry . '">tego znaleziska</a>';

            case Log::TYPE_SINGLE_LINK_CALL_VOTERS_AND_COMMENTERS_COMMENT:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zawołał plusujących <a rel="nofollow" href="' . $_ENV['WYKOP_BASE_URL'] . 'wpis/' . $log->single_source_entry . '/#comment-' . $log->single_source_comment . '">ten komentarz</a> i komentujących <a rel="nofollow" href="http://wykop.pl/wpis/' . $log->single_source_entry . '">ten wpis</a> do <a rel="nofollow" href="http://wykop.pl/link/' . $log->single_entry . '">tego znaleziska</a>';

            case Log::TYPE_SINGLE_LINK_CALL_OWNERS:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zawołał właścicieli spamlist do <a rel="nofollow" href="' . $_ENV['WYKOP_BASE_URL'] . 'link/' . $log->single_entry . '">tego znaleziska</a>';

            case Log::TYPE_SINGLE_LINK_CALL_LINK_DIGS:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zawołał wykopujących <a rel="nofollow" href="' . $_ENV['WYKOP_BASE_URL'] . 'link/' . $log->single_source_entry . '">to znalezisko</a> do <a rel="nofollow" href="http://wykop.pl/link/' . $log->single_entry . '">tego znaleziska</a>';

            case Log::TYPE_SINGLE_LINK_CALL_LINK_BURIES:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zawołał zakopujących <a rel="nofollow" href="' . $_ENV['WYKOP_BASE_URL'] . 'link/' . $log->single_source_entry . '">to znalezisko</a> do <a rel="nofollow" href="http://wykop.pl/link/' . $log->single_entry . '">tego znaleziska</a>';

            case Log::TYPE_SINGLE_LINK_CALL_LINK_COMMENTERS:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zawołał komentujących <a rel="nofollow" href="' . $_ENV['WYKOP_BASE_URL'] . 'link/' . $log->single_source_entry . '">to znalezisko</a> do <a rel="nofollow" href="http://wykop.pl/link/' . $log->single_entry . '">tego znaleziska</a>';

            case Log::TYPE_SINGLE_LINK_CALL_LINK_ALL:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zawołał aktywnych w <a rel="nofollow" href="' . $_ENV['WYKOP_BASE_URL'] . 'link/' . $log->single_source_entry . '">tym znalezisku</a> do <a rel="nofollow" href="http://wykop.pl/link/' . $log->single_entry . '">tego znaleziska</a>';

            default:
                return 'nieznane wydarzenie';
        }
    }

    protected function getFemaleLogText($log) {
        switch ($log->type) {
            case Log::TYPE_CALL:
                if ($log->call->link_id !== null) {
                    return $this->templateService->getUserProfileUrl($log->user)
                        . ' zawołała do <a href="' . $_ENV['WYKOP_BASE_URL'] . 'link/' . $log->call->link_id . '" rel="nofollow">znaleziska</a>';
                } else {
                    return $this->templateService->getUserProfileUrl($log->user)
                        . ' zawołała do <a href="' . $_ENV['WYKOP_BASE_URL'] . 'wpis/' . $log->call->entry_id . '" rel="nofollow">wpisu</a>';
                }

            case Log::TYPE_JOINED_SELF:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' dołączyła do listy';

            case Log::TYPE_JOINED_ADMIN:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' dodała do listy użytkownika <strong>' . $this->templateService->getUserProfileUrl($log->subject) . '</strong>';

            case Log::TYPE_LEFT_SELF:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' opuściła listę';

            case Log::TYPE_LEFT_ADMIN:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' usunęła użytkownika <strong>' . $this->templateService->getUserProfileUrl($log->subject) . '</strong> z listy';

            case Log::TYPE_RIGHTS_CHANGE:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zmieniła uprawnienia użytkownika ' . $this->templateService->getUserProfileUrl($log->subject);

            case Log::TYPE_RIGHTS_BANNED:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zbanowała użytkownika ' . $this->templateService->getUserProfileUrl($log->subject);

            case Log::TYPE_RIGHTS_CALL_OPTOUT_ON:
                if (!empty($log->subject)) {
                    return $this->templateService->getUserProfileUrl($log->user)
                        . ' włączyła blokadę wołania użytkownika ' . $this->templateService->getUserProfileUrl($log->subject);
                } else {
                    return $this->templateService->getUserProfileUrl($log->user)
                        . ' włączyła blokadę wołania';
                }

            case Log::TYPE_RIGHTS_CALL_OPTOUT_OFF:
                if (!empty($log->subject)) {
                    return $this->templateService->getUserProfileUrl($log->user)
                        . ' wyłączyła blokadę wołania użytkownika ' . $this->templateService->getUserProfileUrl($log->subject);
                } else {
                    return $this->templateService->getUserProfileUrl($log->user)
                        . ' wyłączyła blokadę wołania';
                }

            case Log::TYPE_CREATED:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' utworzyła listę';

            case Log::TYPE_DELETED:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' usunęła listę';

            case Log::TYPE_EDITED:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' edytowała listę';

            case Log::TYPE_SINGLE_CALL_VOTERS:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zawołała plusujących <a rel="nofollow" href="' . $_ENV['WYKOP_BASE_URL'] . 'wpis/' . $log->single_source_entry . '">ten wpis</a> do <a rel="nofollow" href="http://wykop.pl/wpis/' . $log->single_entry . '">tego wpisu</a>';

            case Log::TYPE_SINGLE_CALL_VOTERS_COMMENT:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zawołała plusujących <a rel="nofollow" href="' . $_ENV['WYKOP_BASE_URL'] . 'wpis/' . $log->single_source_entry . '/#comment-' . $log->single_source_comment . '">ten komentarz</a> do <a rel="nofollow" href="http://wykop.pl/wpis/' . $log->single_entry . '">tego wpisu</a>';

            case Log::TYPE_SINGLE_CALL_COMMENTERS:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zawołała komentujących <a rel="nofollow" href="' . $_ENV['WYKOP_BASE_URL'] . 'wpis/' . $log->single_source_entry . '">ten wpis</a> do <a rel="nofollow" href="http://wykop.pl/wpis/' . $log->single_entry . '">tego wpisu</a>';

            case Log::TYPE_SINGLE_CALL_VOTERS_AND_COMMENTERS:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zawołała plusujących i komentujących <a rel="nofollow" href="' . $_ENV['WYKOP_BASE_URL'] . 'wpis/' . $log->single_source_entry . '">ten wpis</a> do <a rel="nofollow" href="http://wykop.pl/wpis/' . $log->single_entry . '">tego wpisu</a>';

            case Log::TYPE_SINGLE_CALL_VOTERS_AND_COMMENTERS_COMMENT:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zawołała plusujących <a rel="nofollow" href="' . $_ENV['WYKOP_BASE_URL'] . 'wpis/' . $log->single_source_entry . '/#comment-' . $log->single_source_comment . '">ten komentarz</a> i komentujących <a rel="nofollow" href="http://wykop.pl/wpis/' . $log->single_source_entry . '">ten wpis</a> do <a rel="nofollow" href="http://wykop.pl/wpis/' . $log->single_entry . '">tego wpisu</a>';

            case Log::TYPE_SINGLE_CALL_OWNERS:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zawołała właścicieli spamlist do <a rel="nofollow" href="' . $_ENV['WYKOP_BASE_URL'] . 'wpis/' . $log->single_entry . '">tego wpisu</a>';

            case Log::TYPE_SINGLE_CALL_LINK_DIGS:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zawołała wykopujących <a rel="nofollow" href="' . $_ENV['WYKOP_BASE_URL'] . 'link/' . $log->single_source_entry . '">to znalezisko</a> do <a rel="nofollow" href="http://wykop.pl/wpis/' . $log->single_entry . '">tego wpisu</a>';

            case Log::TYPE_SINGLE_CALL_LINK_BURIES:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zawołała zakopujących <a rel="nofollow" href="' . $_ENV['WYKOP_BASE_URL'] . 'link/' . $log->single_source_entry . '">to znalezisko</a> do <a rel="nofollow" href="http://wykop.pl/wpis/' . $log->single_entry . '">tego wpisu</a>';

            case Log::TYPE_SINGLE_CALL_LINK_COMMENTERS:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zawołała komentujących <a rel="nofollow" href="' . $_ENV['WYKOP_BASE_URL'] . 'link/' . $log->single_source_entry . '">to znalezisko</a> do <a rel="nofollow" href="http://wykop.pl/wpis/' . $log->single_entry . '">tego wpisu</a>';

            case Log::TYPE_SINGLE_CALL_LINK_ALL:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zawołała aktywnych w <a rel="nofollow" href="' . $_ENV['WYKOP_BASE_URL'] . 'link/' . $log->single_source_entry . '">tym znalezisku</a> do <a rel="nofollow" href="http://wykop.pl/wpis/' . $log->single_entry . '">tego wpisu</a>';

            case Log::TYPE_SINGLE_LINK_CALL_VOTERS:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zawołała plusujących <a rel="nofollow" href="' . $_ENV['WYKOP_BASE_URL'] . 'wpis/' . $log->single_source_entry . '">ten wpis</a> do <a rel="nofollow" href="http://wykop.pl/link/' . $log->single_entry . '">tego znaleziska</a>';

            case Log::TYPE_SINGLE_LINK_CALL_VOTERS_COMMENT:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zawołała plusujących <a rel="nofollow" href="' . $_ENV['WYKOP_BASE_URL'] . 'wpis/' . $log->single_source_entry . '/#comment-' . $log->single_source_comment . '">ten komentarz</a> do <a rel="nofollow" href="http://wykop.pl/link/' . $log->single_entry . '">tego znaleziska</a>';

            case Log::TYPE_SINGLE_LINK_CALL_COMMENTERS:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zawołała komentujących <a rel="nofollow" href="' . $_ENV['WYKOP_BASE_URL'] . 'wpis/' . $log->single_source_entry . '">ten wpis</a> do <a rel="nofollow" href="http://wykop.pl/link/' . $log->single_entry . '">tego znaleziska</a>';

            case Log::TYPE_SINGLE_LINK_CALL_VOTERS_AND_COMMENTERS:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zawołała plusujących i komentujących <a rel="nofollow" href="' . $_ENV['WYKOP_BASE_URL'] . 'wpis/' . $log->single_source_entry . '">ten wpis</a> do <a rel="nofollow" href="http://wykop.pl/link/' . $log->single_entry . '">tego znaleziska</a>';

            case Log::TYPE_SINGLE_LINK_CALL_VOTERS_AND_COMMENTERS_COMMENT:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zawołała plusujących <a rel="nofollow" href="' . $_ENV['WYKOP_BASE_URL'] . 'wpis/' . $log->single_source_entry . '/#comment-' . $log->single_source_comment . '">ten komentarz</a> i komentujących <a rel="nofollow" href="http://wykop.pl/wpis/' . $log->single_source_entry . '">ten wpis</a> do <a rel="nofollow" href="http://wykop.pl/link/' . $log->single_entry . '">tego znaleziska</a>';

            case Log::TYPE_SINGLE_LINK_CALL_OWNERS:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zawołała właścicieli spamlist do <a rel="nofollow" href="' . $_ENV['WYKOP_BASE_URL'] . 'link/' . $log->single_entry . '">tego znaleziska</a>';

            case Log::TYPE_SINGLE_LINK_CALL_LINK_DIGS:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zawołała wykopujących <a rel="nofollow" href="' . $_ENV['WYKOP_BASE_URL'] . 'link/' . $log->single_source_entry . '">to znalezisko</a> do <a rel="nofollow" href="http://wykop.pl/link/' . $log->single_entry . '">tego znaleziska</a>';

            case Log::TYPE_SINGLE_LINK_CALL_LINK_BURIES:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zawołała zakopujących <a rel="nofollow" href="' . $_ENV['WYKOP_BASE_URL'] . 'link/' . $log->single_source_entry . '">to znalezisko</a> do <a rel="nofollow" href="http://wykop.pl/link/' . $log->single_entry . '">tego znaleziska</a>';

            case Log::TYPE_SINGLE_LINK_CALL_LINK_COMMENTERS:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zawołała komentujących <a rel="nofollow" href="' . $_ENV['WYKOP_BASE_URL'] . 'link/' . $log->single_source_entry . '">to znalezisko</a> do <a rel="nofollow" href="http://wykop.pl/link/' . $log->single_entry . '">tego znaleziska</a>';

            case Log::TYPE_SINGLE_LINK_CALL_LINK_ALL:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zawołała aktywnych w <a rel="nofollow" href="' . $_ENV['WYKOP_BASE_URL'] . 'link/' . $log->single_source_entry . '">tym znalezisku</a> do <a rel="nofollow" href="http://wykop.pl/link/' . $log->single_entry . '">tego znaleziska</a>';

            default:
                return 'nieznane wydarzenie';
        }
    }

    protected function getUnknownSexLogText($log) {switch ($log->type) {
            case Log::TYPE_CALL:
                if ($log->call->link_id !== null) {
                    return $this->templateService->getUserProfileUrl($log->user)
                        . ' zawołał(a) do <a href="' . $_ENV['WYKOP_BASE_URL'] . 'link/' . $log->call->link_id . '" rel="nofollow">znaleziska</a>';
                } else {
                    return $this->templateService->getUserProfileUrl($log->user)
                        . ' zawołał(a) do <a href="' . $_ENV['WYKOP_BASE_URL'] . 'wpis/' . $log->call->entry_id . '" rel="nofollow">wpisu</a>';
                }

            case Log::TYPE_JOINED_SELF:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' dołączył(a) do listy';

            case Log::TYPE_JOINED_ADMIN:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' dodał(a) do listy użytkownika <strong>' . $this->templateService->getUserProfileUrl($log->subject) . '</strong>';

            case Log::TYPE_LEFT_SELF:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' opuścił(a) listę';

            case Log::TYPE_LEFT_ADMIN:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' usunął/usunęła użytkownika <strong>' . $this->templateService->getUserProfileUrl($log->subject) . '</strong> z listy';

            case Log::TYPE_RIGHTS_CHANGE:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zmienił(a) uprawnienia użytkownika ' . $this->templateService->getUserProfileUrl($log->subject);

            case Log::TYPE_RIGHTS_BANNED:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zbanował(a) użytkownika ' . $this->templateService->getUserProfileUrl($log->subject);

            case Log::TYPE_RIGHTS_CALL_OPTOUT_ON:
                if (!empty($log->subject)) {
                    return $this->templateService->getUserProfileUrl($log->user)
                        . ' włączył(a) blokadę wołania użytkownika ' . $this->templateService->getUserProfileUrl($log->subject);
                } else {
                    return $this->templateService->getUserProfileUrl($log->user)
                        . ' włączył(a) blokadę wołania';
                }

            case Log::TYPE_RIGHTS_CALL_OPTOUT_OFF:
                if (!empty($log->subject)) {
                    return $this->templateService->getUserProfileUrl($log->user)
                        . ' wyłączył(a) blokadę wołania użytkownika ' . $this->templateService->getUserProfileUrl($log->subject);
                } else {
                    return $this->templateService->getUserProfileUrl($log->user)
                        . ' wyłączył(a) blokadę wołania';
                }

            case Log::TYPE_CREATED:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' utworzył(a) listę';

            case Log::TYPE_DELETED:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' usunął/usunęła listę';

            case Log::TYPE_EDITED:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' edytował(a) listę';

            case Log::TYPE_SINGLE_CALL_VOTERS:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zawołał(a) plusujących <a rel="nofollow" href="' . $_ENV['WYKOP_BASE_URL'] . 'wpis/' . $log->single_source_entry . '">ten wpis</a> do <a rel="nofollow" href="http://wykop.pl/wpis/' . $log->single_entry . '">tego wpisu</a>';

            case Log::TYPE_SINGLE_CALL_VOTERS_COMMENT:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zawołał(a) plusujących <a rel="nofollow" href="' . $_ENV['WYKOP_BASE_URL'] . 'wpis/' . $log->single_source_entry . '/#comment-' . $log->single_source_comment . '">ten komentarz</a> do <a rel="nofollow" href="http://wykop.pl/wpis/' . $log->single_entry . '">tego wpisu</a>';

            case Log::TYPE_SINGLE_CALL_COMMENTERS:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zawołał(a) komentujących <a rel="nofollow" href="' . $_ENV['WYKOP_BASE_URL'] . 'wpis/' . $log->single_source_entry . '">ten wpis</a> do <a rel="nofollow" href="http://wykop.pl/wpis/' . $log->single_entry . '">tego wpisu</a>';

            case Log::TYPE_SINGLE_CALL_VOTERS_AND_COMMENTERS:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zawołał(a) plusujących i komentujących <a rel="nofollow" href="' . $_ENV['WYKOP_BASE_URL'] . 'wpis/' . $log->single_source_entry . '">ten wpis</a> do <a rel="nofollow" href="http://wykop.pl/wpis/' . $log->single_entry . '">tego wpisu</a>';

            case Log::TYPE_SINGLE_CALL_VOTERS_AND_COMMENTERS_COMMENT:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zawołał(a) plusujących <a rel="nofollow" href="' . $_ENV['WYKOP_BASE_URL'] . 'wpis/' . $log->single_source_entry . '/#comment-' . $log->single_source_comment . '">ten komentarz</a> i komentujących <a rel="nofollow" href="http://wykop.pl/wpis/' . $log->single_source_entry . '">ten wpis</a> do <a rel="nofollow" href="http://wykop.pl/wpis/' . $log->single_entry . '">tego wpisu</a>';

            case Log::TYPE_SINGLE_CALL_OWNERS:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zawołał(a) właścicieli spamlist do <a rel="nofollow" href="' . $_ENV['WYKOP_BASE_URL'] . 'wpis/' . $log->single_entry . '">tego wpisu</a>';

            case Log::TYPE_SINGLE_CALL_LINK_DIGS:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zawołał(a) wykopujących <a rel="nofollow" href="' . $_ENV['WYKOP_BASE_URL'] . 'link/' . $log->single_source_entry . '">to znalezisko</a> do <a rel="nofollow" href="http://wykop.pl/wpis/' . $log->single_entry . '">tego wpisu</a>';

            case Log::TYPE_SINGLE_CALL_LINK_BURIES:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zawołał(a) zakopujących <a rel="nofollow" href="' . $_ENV['WYKOP_BASE_URL'] . 'link/' . $log->single_source_entry . '">to znalezisko</a> do <a rel="nofollow" href="http://wykop.pl/wpis/' . $log->single_entry . '">tego wpisu</a>';

            case Log::TYPE_SINGLE_CALL_LINK_COMMENTERS:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zawołał(a) komentujących <a rel="nofollow" href="' . $_ENV['WYKOP_BASE_URL'] . 'link/' . $log->single_source_entry . '">to znalezisko</a> do <a rel="nofollow" href="http://wykop.pl/wpis/' . $log->single_entry . '">tego wpisu</a>';

            case Log::TYPE_SINGLE_CALL_LINK_ALL:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zawołał(a) aktywnych w <a rel="nofollow" href="' . $_ENV['WYKOP_BASE_URL'] . 'link/' . $log->single_source_entry . '">tym znalezisku</a> do <a rel="nofollow" href="http://wykop.pl/wpis/' . $log->single_entry . '">tego wpisu</a>';

            case Log::TYPE_SINGLE_LINK_CALL_VOTERS:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zawołał(a) plusujących <a rel="nofollow" href="' . $_ENV['WYKOP_BASE_URL'] . 'wpis/' . $log->single_source_entry . '">ten wpis</a> do <a rel="nofollow" href="http://wykop.pl/link/' . $log->single_entry . '">tego znaleziska</a>';

            case Log::TYPE_SINGLE_LINK_CALL_VOTERS_COMMENT:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zawołał(a) plusujących <a rel="nofollow" href="' . $_ENV['WYKOP_BASE_URL'] . 'wpis/' . $log->single_source_entry . '/#comment-' . $log->single_source_comment . '">ten komentarz</a> do <a rel="nofollow" href="http://wykop.pl/link/' . $log->single_entry . '">tego znaleziska</a>';

            case Log::TYPE_SINGLE_LINK_CALL_COMMENTERS:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zawołał(a) komentujących <a rel="nofollow" href="' . $_ENV['WYKOP_BASE_URL'] . 'wpis/' . $log->single_source_entry . '">ten wpis</a> do <a rel="nofollow" href="http://wykop.pl/link/' . $log->single_entry . '">tego znaleziska</a>';

            case Log::TYPE_SINGLE_LINK_CALL_VOTERS_AND_COMMENTERS:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zawołał(a) plusujących i komentujących <a rel="nofollow" href="' . $_ENV['WYKOP_BASE_URL'] . 'wpis/' . $log->single_source_entry . '">ten wpis</a> do <a rel="nofollow" href="http://wykop.pl/link/' . $log->single_entry . '">tego znaleziska</a>';

            case Log::TYPE_SINGLE_LINK_CALL_VOTERS_AND_COMMENTERS_COMMENT:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zawołał(a) plusujących <a rel="nofollow" href="' . $_ENV['WYKOP_BASE_URL'] . 'wpis/' . $log->single_source_entry . '/#comment-' . $log->single_source_comment . '">ten komentarz</a> i komentujących <a rel="nofollow" href="http://wykop.pl/wpis/' . $log->single_source_entry . '">ten wpis</a> do <a rel="nofollow" href="http://wykop.pl/link/' . $log->single_entry . '">tego znaleziska</a>';

            case Log::TYPE_SINGLE_LINK_CALL_OWNERS:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zawołał(a) właścicieli spamlist do <a rel="nofollow" href="' . $_ENV['WYKOP_BASE_URL'] . 'link/' . $log->single_entry . '">tego znaleziska</a>';

            case Log::TYPE_SINGLE_LINK_CALL_LINK_DIGS:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zawołał(a) wykopujących <a rel="nofollow" href="' . $_ENV['WYKOP_BASE_URL'] . 'link/' . $log->single_source_entry . '">to znalezisko</a> do <a rel="nofollow" href="http://wykop.pl/link/' . $log->single_entry . '">tego znaleziska</a>';

            case Log::TYPE_SINGLE_LINK_CALL_LINK_BURIES:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zawołał(a) zakopujących <a rel="nofollow" href="' . $_ENV['WYKOP_BASE_URL'] . 'link/' . $log->single_source_entry . '">to znalezisko</a> do <a rel="nofollow" href="http://wykop.pl/link/' . $log->single_entry . '">tego znaleziska</a>';

            case Log::TYPE_SINGLE_LINK_CALL_LINK_COMMENTERS:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zawołał(a) komentujących <a rel="nofollow" href="' . $_ENV['WYKOP_BASE_URL'] . 'link/' . $log->single_source_entry . '">to znalezisko</a> do <a rel="nofollow" href="http://wykop.pl/link/' . $log->single_entry . '">tego znaleziska</a>';

            case Log::TYPE_SINGLE_LINK_CALL_LINK_ALL:
                return $this->templateService->getUserProfileUrl($log->user)
                    . ' zawołał(a) aktywnych w <a rel="nofollow" href="' . $_ENV['WYKOP_BASE_URL'] . 'link/' . $log->single_source_entry . '">tym znalezisku</a> do <a rel="nofollow" href="http://wykop.pl/link/' . $log->single_entry . '">tego znaleziska</a>';

            default:
                return 'nieznane wydarzenie';
        }
    }
}