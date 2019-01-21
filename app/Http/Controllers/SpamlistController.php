<?php
namespace App\Http\Controllers;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\Spamlist;
use WykoCommon\Services\UserService;
use App\Services\UserService as ApiUserService;
use App\Services\CallService;
use WykoCommon\Services\WykopService;
use App\Services\SpamlistService;
use WykoCommon\Services\PaginationService;
use WykoCommon\Models\Queue;
use App\Models\Call;
use App\Models\Log;
use App\Models\UserSpamlist;
use App\Models\User;
use App\Models\Category;
use App\Models\City;

class SpamlistController extends Controller
{
    public function index(Request $request, PaginationService $paginationService) {
        $validator = Validator::make($request->all(), [
            'categoryId' => 'integer|exists:categories,id',
            'cityId' => 'integer|exists:cities,id'
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $request->session()->flash('flashError', implode("<br />", $messages));

            return redirect('/');
        }

        $queryBuilder = Spamlist::orderBy('joined_count', 'DESC');

        if ($request->has('categoryId')) {
            $queryBuilder->where('category_id', '=', $request->get('categoryId'));
        }

        if ($request->has('cityId')) {
            $queryBuilder->where('city_id', '=', $request->get('cityId'));
        }

        $queryBuilder->with('user');

        $paginator = $paginationService->createPaginator($queryBuilder, 25);
        if ($paginator === false) {
            return response(view('errors/404'), 404);
        }

        return view('spamlist/index', array(
            'paginator' => $paginator,
            'categories' => Category::all(),
            'cities' => City::all(),
            'categoryId' => (int) $request->get('categoryId', null),
            'cityId' => (int) $request->get('cityId', null)
        ));
    }

    public function get(UserService $userService, SpamlistService $spamlistService, PaginationService $paginationService, $uid) {
        $entity = Spamlist::withTrashed()
                ->where('uid', '=', $uid)
                ->with(array(
                    'logs' => function($query) {
                        $query->orderBy('created_at', 'desc')->take(10);
                    }
                ))
                ->with('city')
                ->with('user')
                ->with('category')
                ->first();

        if ($entity === null) {
            return response(view('errors/404'), 404);
        }

        if ($entity->trashed()) {
            return view('spamlist/deleted', array(
                'item' => $entity
            ));
        }

        $callsQueryBuilder = $entity->calls()->orderBy('updated_at', 'desc');

        $callsPaginator = $paginationService->createPaginator($callsQueryBuilder);
        if ($callsPaginator === false) {
            return response(view('errors/404'), 404);
        }

        $user = $userService->getCurrentUser();

        $isOwner = false;
        if ($user !== null) {
            if ($entity['user_id'] == $user['id'] || $user->rights == User::RIGHTS_ADMIN) {
                $isOwner = true;
            }
        }

        $isOnList = false;
        if ($user !== null && $user->spamlists->contains($entity['id'])) {
            $isOnList = true;
        }

        $joinable = true;
        if ($user === null || $isOwner) {
            $joinable = false;
        }

        return view('spamlist/get', array(
            'item' => $entity,
            'logs' => $entity->logs,
            'callsPaginator' => $callsPaginator,
            'isOwner' => $isOwner,
            'isOnList' => $isOnList,
            'joinable' => $joinable,
            'canEdit' => $spamlistService->checkRights($entity, $user, UserSpamlist::ACTION_EDIT),
            'rightsExtended' => ($user !== null && $user->rights >= User::RIGHTS_EXTENDED),
            'canCall' => $spamlistService->checkRights($entity, $user, UserSpamlist::ACTION_CALL)
        ));
    }

    public function editForm(UserService $userService, SpamlistService $spamlistService, $uid) {
        $entity = Spamlist::where('uid', '=', $uid)
                ->with('city')
                ->first();

        if ($entity === null) {
            return response(view('errors/404'), 404);
        }

        $user = $userService->getCurrentUser();

        if (!$spamlistService->checkRights($entity, $user, UserSpamlist::ACTION_EDIT)) {
            return response(view('errors/404'), 404);
        }

        return view('spamlist/edit', array(
            'item' => $entity,
            'categories' => Category::all()
        ));
    }

    public function edit(Request $request, UserService $userService, SpamlistService $spamlistService, $uid) {
        $entity = Spamlist::where('uid', '=', $uid)
                ->first();

        if ($entity === null) {
            return response(view('errors/404'), 404);
        }

        $user = $userService->getCurrentUser();

        if (!$spamlistService->checkRights($entity, $user, UserSpamlist::ACTION_EDIT)) {
            return response(view('errors/404'), 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:150',
            'description' => 'required|max:999',
            'city' => 'max:150',
            'categoryId' => 'integer|exists:categories,id'
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $request->session()->flash('flashError', implode("<br />", $messages));

            return redirect()->back()->withInput();
        }

        $cityId = null;
        if (strlen($request->get('city')) > 0) {
            $cityEntity = City::where('name', '=', $request->get('city'))->first();

            if ($cityEntity === null) {
                $cityEntity = new City();

                $cityEntity->name = $request->get('city');

                $cityEntity->save();
            }

            $cityId = $cityEntity->id;
        }

        $categoryId = null;
        if ($request->has('categoryId')) {
            $categoryId = (int) $request->get('categoryId');
        }

        $log = new Log();

        $log->user_id = $user->id;
        $log->type = Log::TYPE_EDITED;
        $log->spamlist_id = $entity->id;

        $log->save();

        $entity->name = $request->get('name');
        $entity->description = $request->get('description');
        $entity->city_id = $cityId;
        $entity->category_id = $categoryId;

        $entity->save();

        $request->session()->flash('flashSuccess', 'Zmiany zapisane');

        return redirect()->route('getSpamlistUrl', array(
            'uid' => $entity->uid
        ));
    }

    public function addForm() {
        return view('spamlist/add', array(
            'categories' => Category::all()
        ));
    }

    public function add(Request $request, UserService $userService) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:150',
            'description' => 'required|max:999',
            'city' => 'max:150',
            'categoryId' => 'integer|exists:categories,id'
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $request->session()->flash('flashError', implode("<br />", $messages));

            return redirect()->back()->withInput();
        }

        $user = $userService->getCurrentUser();

        if ($user === null) {
            $request->session()->flash('flashError', 'Musisz się zalogować');

            return redirect()->back();
        }

        $cityId = null;
        if (strlen($request->get('city')) > 0) {
            $cityEntity = City::where('name', '=', $request->get('city'))->first();

            if ($cityEntity === null) {
                $cityEntity = new City();

                $cityEntity->name = $request->get('city');

                $cityEntity->save();
            }

            $cityId = $cityEntity->id;
        }

        $categoryId = null;
        if ($request->has('categoryId')) {
            $categoryId = (int) $request->get('categoryId');
        }

        $item = new Spamlist();

        $item->user_id = $user->id;
        $item->name = $request->get('name');
        $item->description = $request->get('description');
        $item->uid = $this->generateUID();
        $item->city_id = $cityId;
        $item->category_id = $categoryId;

        $item->called_count = 0;
        $item->joined_count = 0;

        $item->save();

        $user->created_count++;

        $user->save();

        $log = new Log();

        $log->user_id = $user->id;
        $log->type = Log::TYPE_CREATED;
        $log->spamlist_id = $item->id;

        $log->save();

        $request->session()->flash('flashSuccess', 'Lista dodana');

        return redirect()->route('getSpamlistUrl', array(
            'uid' => $item->uid
        ));
    }

    /**
     * Validates call data, returns true or whatever callback returns
     * Callback accepts one argument that is array of errors
     *
     * @param array $data
     * @param Closure $callback
     * @return bool|mixed
     */
    protected function callValidation(array $data, User $user, Closure $callback)
    {
        $validator = Validator::make($data, [
            'entryUrl' => 'required|URL',
            'spamlists' => 'required|array',
            'sex' => 'required|in:0,1,2'
        ]);
        if ($validator->fails()) {
            return $callback($validator->errors()->all());
        }

        if (empty($data['spamlists'])) {

            return $callback(['Musisz wybrać przynajmniej jedną listę']);
        }

        if (count($data['spamlists']) > 3) {
            return $callback(['Możesz wołać maksymalnie 3 listy na raz']);
        }

        foreach($data['spamlists'] as $key => $value) {
            $validator->mergeRules("spamlists.$key", 'required|regex:/[0-9A-Za-z]{16}/');
        }

        if ($validator->fails()) {
            return $callback($validator->errors()->all());
        }

        if (Cache::has('call_lock_' . $user->id)) {
            $latestCallDate = new \DateTime(Cache::get('call_lock_' . $user->id));

            return $callback(['Musisz poczekać <span class="countdownTimer">' . $latestCallDate->format('Y-m-d H:i:s') . '</span> min zanim zawołasz ponownie.']);
        }

        $currentQueue = Queue::where('user_id', '=', $user->id)
            ->whereNotNull('user_key')
            ->first();

        if ($currentQueue !== null) {
            return $callback(['Twoje poprzednie wołanie jest nadal w trakcie. Musisz poczekać aż zostanie zakończone (może to potrwać do 15 minut).']);
        }

        return true;
    }

    public function call(
        Request $request,
        UserService $userService,
        SpamlistService $spamlistService,
        CallService $callService
    ) {
        if (isset($_ENV['IS_OFFLINE']) && $_ENV['IS_OFFLINE'] == 1) {
            return redirect(route('offline'));
        }
        //get user
        $user = $userService->getCurrentUser();
        //on session out display 404
        if ($user === null) {
            return response(view('errors/404'), 404);
        }

        $valid = $this->callValidation($request->all(), $user, function ($errors) use ($request) {
            $request->session()->flash('flashError', implode("<br />", $errors));
            return false;
        });

        if ($valid !== true) {
            return redirect()->back()->withInput();
        }

        $result = $spamlistService->call($user, $request->get('entryUrl'), (int) $request->get('sex'), $request->get('spamlists'), $callService->getGroupPerComment($request->session()->get('wykopGroup')));
        if ($result !== true) {
            $this->request->session()->flash('flashError', $result);

            return redirect()->back()->withInput();
        }

        return redirect()->back();
    }

    public function apiCall(
        Request $request,
        ApiUserService $userService,
        SpamlistService $spamlistService,
        CallService $callService
    ) {
        if (isset($_ENV['IS_OFFLINE']) && $_ENV['IS_OFFLINE'] == 1) {
            return new JsonResponse('System is offline', Response::HTTP_SERVICE_UNAVAILABLE);
        }

        $user = $userService->getCurrentApiUser();
        $requestBody = $request->json()->all();
        $validationResult = $this->callValidation($requestBody, $user, function ($errors) use ($request) {
            return new JsonResponse($errors, Response::HTTP_BAD_REQUEST);
        });

        if ($validationResult instanceof JsonResponse) {
            return $validationResult;
        }

        $result = $spamlistService->call(
            $user,
            $requestBody['entryUrl'],
            (int) $requestBody['sex'],
            $requestBody['spamlists'],
            $callService->getGroupPerComment($user->color)
        );

        if ($result !== true) {
            return new JsonResponse($result, Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse('OK', Response::HTTP_OK);
    }

    public function join(Request $request, UserService $userService, $uid) {
        $entity = Spamlist::where('uid', '=', $uid)->first();

        if ($entity === null) {
            return response(view('errors/404'), 404);
        }

        $user = $userService->getCurrentUser();

        if ($entity['user_id'] == $user['id']) {
            $request->session()->flash('flashError', 'Nie możesz dołączyć do swojej listy');

            return redirect()->back();
        }
        
        $pivot = UserSpamlist::where('user_id', '=', $user['id'])
                ->where('spamlist_id', '=', $entity['id'])
                ->first();

        if (is_object($pivot)) {
            if ($pivot->rights === UserSpamlist::RIGHTS_BANNED) {
                $request->session()->flash('flashError', 'Nie możesz opuścić listy, na której masz bana.');

                return redirect()->back();
            }

            $pivot->delete();

            $entity->joined_count--;
            $entity->timestamps = false;

            $entity->save();

            $log = new Log();

            $log->user_id = $user->id;
            $log->type = Log::TYPE_LEFT_SELF;
            $log->spamlist_id = $entity->id;

            $log->save();

            $user->joined_count--;
            $user->timestamps = false;

            $user->save();

            $request->session()->flash('flashSuccess', 'Opuściłeś listę');
        } else {
            $pivot = new UserSpamlist();

            $pivot->user_id = $user['id'];
            $pivot->spamlist_id = $entity['id'];

            $pivot->save();

            $entity->joined_count++;
            $entity->timestamps = false;

            $entity->save();

            $log = new Log();

            $log->user_id = $user->id;
            $log->type = Log::TYPE_JOINED_SELF;
            $log->spamlist_id = $entity->id;

            $log->save();

            $user->joined_count++;
            $user->timestamps = false;

            $user->save();

            $request->session()->flash('flashSuccess', 'Dołączyłeś do listy');
        }

        return redirect()->back();
    }

    public function import(Request $request, UserService $userService, $uid) {
        $entity = Spamlist::where('uid', '=', $uid)->first();

        $user = $userService->getCurrentUser();

        if ($entity === null
                || $user === null) {
            return response(view('errors/404'), 404);
        }

        if ($entity->user_id !== $user->id
                && $user->rights != User::RIGHTS_ADMIN) {
            return response(view('errors/404'), 404);
        }

        $validator = Validator::make($request->all(), [
            'importUsers' => 'required'
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $request->session()->flash('flashError', implode("<br />", $messages));

            return redirect()->back()->withInput();
        }

        $nicks = preg_replace('/[^A-Za-z0-9_\-]/', ' ', $request->get('importUsers'));

        $nicks = explode(' ', $nicks);
        
        if (empty($nicks)) {
            $request->session()->flash('flashError', 'Musisz podać nicki do zaimportowania');

            return redirect()->back();
        }

        $imported = 0;
        $ownerNick = strtolower($entity->user->nick);

        foreach ($nicks as $nick) {
            if (strlen($nick) <= 3) {
                continue;
            }

            if (strtolower($nick) === $ownerNick) {
                $request->session()->flash('flashInfo', 'Właściciel listy jest zawsze wołany, nie ma potrzeby dodawania go do listy');

                continue;
            }

            $importedUser = User::where('nick', '=', $nick)
                ->first();

            if ($importedUser === null) {
                $importedUser = new User();

                $importedUser->nick = $nick;
                $importedUser->avatar_url = 'http://xd.cdn02.imgwykop.pl/c3397992/avatar_def,q150.png';
                $importedUser->created_count = 0;
                $importedUser->joined_count = 0;
                $importedUser->called_count = 0;

                $importedUser->save();
            } else {
                $pivot = UserSpamlist::where('user_id', '=', $importedUser->id)
                    ->where('spamlist_id', '=', $entity->id)
                    ->first();

                if (is_object($pivot)) {
                    continue;
                }
            }

            $pivot = new UserSpamlist();

            $pivot->user_id = $importedUser->id;
            $pivot->spamlist_id = $entity->id;

            $pivot->save();

            $entity->joined_count++;
            $entity->timestamps = false;

            $entity->save();

            $log = new Log();

            $log->user_id = $user->id;
            $log->subject_id = $importedUser->id;
            $log->type = Log::TYPE_JOINED_ADMIN;
            $log->spamlist_id = $entity->id;

            $log->save();

            $importedUser->joined_count++;
            $importedUser->timestamps = false;

            $importedUser->save();

            $imported++;
        }

        $request->session()->flash('flashSuccess', 'Import zakończony. Dodani userzy: ' . $imported);

        return redirect()->back();
    }

    public function people(Request $request, UserService $userService, SpamlistService $spamlistService, PaginationService $paginationService, $uid) {
        $entity = Spamlist::where('uid', '=', $uid)->first();

        if ($entity === null) {
            return response(view('errors/404'), 404);
        }

        $defaultSortOption = 'joined.desc';

        $sortOptions = [
            'joined.desc',
            'joined.asc',
            'nick.asc'
        ];

        $sortBy = $request->get('sort', $defaultSortOption);

        if (!in_array($sortBy, $sortOptions)) {
            $sortBy = $defaultSortOption;
        }

        $queryBuilder = UserSpamlist::select(DB::raw('user_spamlists.*, user_spamlists.rights as spamlist_rights, users.nick, users.color, users.rights, users.id as user_id'))
                ->join('users', 'users.id', '=', 'user_spamlists.user_id')
                ->where('spamlist_id', '=', $entity['id'])
                ->orderBy('user_spamlists.rights', 'desc');

        $sortByArray = explode('.', $sortBy);

        if ($sortByArray[0] === 'joined') {
            $sortByField = 'user_spamlists.created_at';
        } else {
            $sortByField = 'users.nick';
        }

        $queryBuilder->orderBy($sortByField, $sortByArray[1]);

        if ($request->has('query')) {
            $queryBuilder->where('users.nick', 'LIKE', '%' . $request->get('query') . '%');
        }

        $paginator = $paginationService->createPaginator($queryBuilder, 50);
        if ($paginator === false) {
            return response(view('errors/404'), 404);
        }

        $user = $userService->getCurrentUser();

        $item = array(
            'uid' => $entity['uid'],
            'name' => $entity['name']
        );

        $isOwner = false;
        if ($entity['user_id'] == $user['id']) {
            $isOwner = true;
        }

        return view('spamlist/people', array(
            'item' => $item,
            'paginator' => $paginator,
            'isOwner' => $isOwner,
            'canChangeRights' => $spamlistService->checkRights($entity, $user, UserSpamlist::ACTION_CHANGE_RIGHTS),
            'query' => $request->get('query', null),
            'sortBy' => $sortBy
        ));
    }

    public function changeRights(Request $request, UserService $userService, SpamlistService $spamlistService, $uid) {
        $validator = Validator::make($request->all(), [
            'userId' => 'required|integer',
            'rights' => 'required|in:-1,2,10,20,99'
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $request->session()->flash('flashError', implode("<br />", $messages));

            return redirect()->back();
        }

        $entity = Spamlist::where('uid', '=', $uid)->first();

        if ($entity === null) {
            return response(view('errors/404'), 404);
        }

        $user = $userService->getCurrentUser();

        if (!$spamlistService->checkRights($entity, $user, UserSpamlist::ACTION_CHANGE_RIGHTS)) {
            return response(view('errors/404'), 404);
        }

        $newRights = (int) $request->get('rights');
		if (($newRights === -1 || $newRights === 2) && $entity->category !== null && $entity->category->limited) {
            $request->session()->flash('flashError', 'Usuwanie użytkowników z list z kategorii ' . $entity->category->name . ' zostało zablokowane');

            return redirect()->back();
		}

        $editedUserId = (int) $request->get('userId');

        $pivot = UserSpamlist::where('user_id', '=', $editedUserId)
                ->where('spamlist_id', '=', $entity->id)
                ->first();

        if ($pivot === null) {
            $request->session()->flash('flashError', 'Ten użytkownik nie jest zapisany');

            return redirect()->back();
        }

        $changeCount = 0;

        if ($newRights === -1) {
            $pivot->delete();

            $log = new Log();

            $log->user_id = $user->id;
            $log->subject_id = $editedUserId;
            $log->type = Log::TYPE_LEFT_ADMIN;
            $log->spamlist_id = $entity->id;

            $log->save();

            $changeCount = -1;
        } else {
            $oldRights = $pivot->rights;

            $pivot->rights = $newRights;

            $pivot->save();

            $log = new Log();

            $log->user_id = $user->id;
            $log->subject_id = $editedUserId;
            $log->type = Log::TYPE_RIGHTS_CHANGE;
            $log->spamlist_id = $entity->id;

            $log->save();

            if ($oldRights != 2 && $pivot->rights == 2) {
                $changeCount = -1;
            } else if ($oldRights == 2 && $pivot->rights != 2) {
                $changeCount = 1;
            }
        }

        if ($changeCount === -1) {
            $editedUser = User::where('id', '=', $editedUserId)->first();

            $editedUser->joined_count--;
            $editedUser->timestamps = false;

            $editedUser->save();

            $entity->joined_count--;
            $entity->timestamps = false;

            $entity->save();
        } else if ($changeCount === 1) {
            $editedUser = User::where('id', '=', $editedUserId)->first();

            $editedUser->joined_count++;
            $editedUser->timestamps = false;

            $editedUser->save();

            $entity->joined_count++;
            $entity->timestamps = false;

            $entity->save();
        }

        $request->session()->flash('flashSuccess', 'Uprawnienia zmienione');

        return redirect()->back();
    }

    public function delete(Request $request, UserService $userService, $uid) {
        $entity = Spamlist::where('uid', '=', $uid)->first();

        if ($entity === null) {
            return response(view('errors/404'), 404);
        }

        $user = $userService->getCurrentUser();

        if ($entity->user_id !== $user->id
                && $user->rights != 99) {
            return response(view('errors/404'), 404);
        }

        $log = new Log();

        $log->user_id = $user->id;
        $log->type = Log::TYPE_DELETED;
        $log->spamlist_id = $entity->id;

        $log->save();

        $user->created_count--;

        $user->save();

        $pivotEntities = UserSpamlist::where('spamlist_id', '=', $entity->id)
                ->where('rights', '!=', 2)
                ->get();

        foreach ($pivotEntities as $loopItem) {
            $loopItem->user->joined_count--;

            $loopItem->user->save();
        }

        $entity->deleted_by = $user->id;
        
        $entity->save();

        $entity->delete();

        $request->session()->flash('flashSuccess', 'Lista została usunięta');

        return redirect()->back();
    }

    private function generateUID() {
        $uid = Str::random(16);

        if (Spamlist::where('uid', '=', $uid)->count() === 0) {
            return $uid;
        } else {
            return $this->generateUID();
        }
    }
}
