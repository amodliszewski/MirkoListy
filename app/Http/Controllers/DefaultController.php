<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Log;
use App\Models\User;
use App\Models\Spamlist;
use App\Models\Call;

class DefaultController extends Controller
{
    public function index(Request $request) {
        $popularEntities = Spamlist::orderBy('joined_count', 'DESC')->limit(3)->get();
        $popularItems = array();

        if ($popularEntities->count() > 0) {
            foreach ($popularEntities as $entity) {
                $popularItems[] = array(
                    'uid' => $entity['uid'],
                    'name' => $entity['name'],
                    'description' => $entity['description'],
                    'joined_count' => $entity['joined_count'],
                    'called_count' => $entity['called_count']
                );
            }
        }

        $limit = 25;
        if ($request->has('logs')) {
            $limit = 200;
        }

        $latestLogs = Log::orderBy('created_at', 'DESC')
                ->with(['spamlist' => function($query) {
                    $query->withTrashed();
                }])
                ->with('user')
                ->limit($limit)
                ->get();

        return view('default/index', array(
            'popular' => $popularItems,
            'latestLogs' => $latestLogs,
            'statsUsers' => User::count(),
            'statsSpamlists' => Spamlist::count(),
            'statsCalls' => Call::count()
        ));
    }
}