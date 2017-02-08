<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Log;
use App\Models\Spamlist;
use WykoCommon\Services\PaginationService;

class LogsController extends Controller
{
    public function index(Request $request, PaginationService $paginationService, $spamlistUid = null) {
        $queryBuilder = Log::select(DB::raw('logs.*, user.nick as user_nick'))
                ->leftJoin('users as user', 'user.id', '=', 'logs.user_id')
                ->leftJoin('users as subject', 'subject.id', '=', 'logs.subject_id')
                ->orderBy('created_at', 'desc');

        $spamlist = null;
        if ($spamlistUid !== null) {
            $spamlist = Spamlist::withTrashed()
                    ->where('uid', '=', $spamlistUid)
                    ->first();

            if ($spamlist === null) {
                return response(view('errors/404'), 404);
            }

            $queryBuilder->where('spamlist_id', $spamlist->id);
        }

        if ($request->has('query')) {
            $queryBuilder->where(function ($query) use ($request) {
                $query->orWhere('user.nick', 'LIKE', '%' . $request->get('query') . '%');
                $query->orWhere('subject.nick', 'LIKE', '%' . $request->get('query') . '%');
            });
        }

        $paginator = $paginationService->createPaginator($queryBuilder, 100);
        if ($paginator === false) {
            return response(view('errors/404'), 404);
        }

        return view('logs/index', array(
            'query' => $request->get('query', null),
            'paginator' => $paginator,
            'spamlist' => $spamlist
        ));
    }
}