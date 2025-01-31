<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Infosperso;
use App\Models\User;
use App\Models\Street;
use App\Models\Building;
use App\Models\Openspace;
use App\Models\Opinion;
use App\Models\Opinion_de;
use App\Models\Comment_en;
use App\Models\Comment_de;
use App\Models\Observation;
use App\Models\Pages;
use App\Models\Place;
use App\Models\PlaceLike;
use App\Models\PlaceComment;
use App\Models\Feeling;
use App\Models\PlaceDetails;
use App\Models\PlaceDetailPlace;
use App\Models\PlaceDetailObservation;
use App\Models\Stat;
use App\Models\Preference;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GlobalController extends Controller
{

    public $place_id;
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */


    public function getAll($edit_id = '')
    {

        if (backpack_auth()->check()) {
            $userid = backpack_auth()->user()->id;

            // check for edit
            if ($edit_id) {
                $checkplace = PlaceDetails::find($edit_id);
                if (!($checkplace && $checkplace->user_id == $userid)) {
                    return redirect('/');
                }
            }


            if (Infosperso::where('user_id', $userid)->exists()) {
                $infos = Infosperso::where('user_id', $userid)->first();

                $allPlaces = Place::where('user_id', null)
                    ->where('parent_id', NULL)
                    ->orWhere('user_id', backpack_auth()->user()->id)
                    ->get();

                // dd($allPlaces);

                $allObservations = Observation::where('user_id', null)
                    ->where('parent_id', NULL)
                    ->orWhere('user_id', backpack_auth()->user()->id)
                    ->get();



                $query = PlaceDetails::whereNotNull('latitude')->whereNotNull('longitude')
                    ->with([
                        'placeDetail',
                        'placeDetail.place',
                        'placeDetail.placeChild',
                        'observationsDetail',
                        'observationsDetail.observation',
                        'observationsDetail.observationChild',
                        'observationsDetail.feeling',
                        'user',
                        'placeComment' => function ($query) {
                            $query->where('user_id', backpack_auth()->user()->id);
                        }
                    ]);

                if (session()->has('placeIds') && count(session('placeIds')) > 0 && session()->has('observationIds') && count(session('observationIds')) > 0) {

                    $query->where(function ($query) {
                        $query->whereHas('placeDetail', function ($subQuery) {
                            $subQuery->whereIn('place_id', session('placeIds'));
                        });
                    });
                    $query->orWhere(function ($query) {
                        $query->whereHas('observationsDetail', function ($subQuery) {
                            $subQuery->whereIn('observation_id', session('observationIds'));
                        });
                    });
                } elseif (session()->has('placeIds') && count(session('placeIds')) > 0) {
                    $query->where(function ($query) {
                        $query->whereHas('placeDetail', function ($subQuery) {
                            $subQuery->whereIn('place_id', session('placeIds'));
                        });
                    });
                } elseif (session()->has('observationIds') && count(session('observationIds')) > 0) {
                    $query->Where(function ($query) {
                        $query->whereHas('observationsDetail', function ($subQuery) {
                            $subQuery->whereIn('observation_id', session('observationIds'));
                        });
                    });
                }




                // var_dump(session('placeIds'));
                // die();


                $all_data = $query->get();


                $feelings = Feeling::all();

                $edited_place = PlaceDetails::with(['observationsDetail.feeling', 'observationsDetail', 'placeDetail'])->find($edit_id);

                $likedPlaces = backpack_auth()->user()->likedPlaces->pluck('place_detail_id');

                return view(
                    'home',
                    compact(
                        'infos',
                        'all_data',
                        'userid',
                        'allPlaces',
                        'allObservations',
                        'feelings',
                        'likedPlaces',
                        'edited_place',
                        'edit_id'
                    )
                );
            } else {
                $infos = new Infosperso();
                $infos->user_id = $userid;
                $infos->save();
                return view('edit_profile');
            }
        } else {
            return view('index');
        }

        // dd('good');
    }

    // public function getAll()
    // {
    //     return view('index');
    // }

    public function community_achievements()
    {

        $usersWithTotals = User::select('*')
            ->addSelect(['total_places' => PlaceDetailPlace::selectRaw('COUNT(*)')
                ->whereIn('place_detail_id', PlaceDetails::select('id')
                    ->whereColumn('user_id', 'users.id'))])
            ->addSelect(['total_observations' => PlaceDetailObservation::selectRaw('COUNT(*)')
                ->whereIn('place_detail_id', PlaceDetails::select('id')
                    ->whereColumn('user_id', 'users.id'))])
            ->orderBy('score', 'desc')->paginate(10);

        return view('community_acheivements', compact('usersWithTotals'));
    }

    public function loadMore_community_achievements(Request $request)
    {
        $page = $request->get('page');

        $usersWithTotals = User::select('*')
            ->addSelect(['total_places' => PlaceDetailPlace::selectRaw('COUNT(*)')
                ->whereIn('place_detail_id', PlaceDetails::select('id')
                    ->whereColumn('user_id', 'users.id'))])
            ->addSelect(['total_observations' => PlaceDetailObservation::selectRaw('COUNT(*)')
                ->whereIn('place_detail_id', PlaceDetails::select('id')
                    ->whereColumn('user_id', 'users.id'))])
            ->orderBy('score', 'desc')->paginate(10, ['*'], 'page', $page);

        $html = view('item_community_acheivements', compact('usersWithTotals'))->render();

        return response()->json(['html' => $html, 'hasMorePages' => $usersWithTotals->hasMorePages()]);
    }





    // public function profil()
    // {
    //     return view('home');
    // }

    private function haversine($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // in meters
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;

        return $distance;
    }

    public function allbadges()
    {
        $explorer = 0;
        if (backpack_auth()->user()->placeDetails->count() > 0) {
            $explorer = 1;
        }

        $citymaker = 0;
        if (backpack_auth()->user()->observations->count() >= 10) {
            $citymaker = 1;
        }


        $architect = 0;
        $allplace = backpack_auth()->user()->placeDetails->flatMap(function ($placeDetail) {
            return $placeDetail->placeDetail->pluck('place_id');
        });
        if ($allplace->count() >= 20) {
            $architect = 1;
        }

        $flaneur = 0;
        $allobservation = backpack_auth()->user()->placeDetails->flatMap(function ($placeDetail) {
            return $placeDetail->observationsDetail->map(function ($observation) {
                return $observation->only(['place_detail_id', 'observation_id']);
            });
        })->unique();
        if ($allobservation->count() >= 20) {
            $flaneur = 1;
        }


        $urbanist = 0;
        $higherCount = max($allplace->count(), $allobservation->count());
        if ($higherCount >= 30) {
            $urbanist = 1;
        }

        $influencer = 0;
        $alllikes = backpack_auth()->user()->placeDetails->flatMap(function ($placeDetail) {
            return $placeDetail->placeLikes;
        });
        if ($alllikes->count() >= 10) {
            $influencer = 1;
        }

        $star = 0;
        if ($alllikes->count() >= 20) {
            $star = 1;
        }

        $guru = 0;
        if (backpack_auth()->user()->likedPlaces->count() >= 10) {
            $guru = 1;
        }

        $investigator = 0;
        $totalImagesCount = backpack_auth()->user()->placeDetails->sum(function ($placeDetail) {
            $observationImageCount = !empty($placeDetail->obsevation_image) ? 1 : 0;
            $placeImageCount = !empty($placeDetail->place_image) ? 1 : 0;
            return $observationImageCount + $placeImageCount;
        });

        if (($totalImagesCount) >= 10) {
            $investigator = 1;
        }

        $supermapper = 0;
        if (backpack_auth()->user()->score >= 500) {
            $supermapper = 1;
        }

        return compact(
            'explorer',
            'citymaker',
            'architect',
            'flaneur',
            'urbanist',
            'influencer',
            'star',
            'guru',
            'investigator',
            'supermapper',
        );
    }

    public function profile()
    {
        $userid = backpack_auth()->user()->id;
        $name = backpack_auth()->user()->name;
        $score = backpack_auth()->user()->score;
        $infos = Infosperso::where('user_id', $userid)->first();
        $preferences = explode(',', $infos->preferences);
        $preferences = preg_replace('/[^A-Za-z0-9 ]/', '', $preferences);

        $badgeData = $this->allbadges();
        extract($badgeData);



        // dd($citymaker);
        return view(
            'profile',
            compact(
                'name',
                'score',
                'preferences',

                'citymaker',
                'architect',
                'explorer',


            )
        );
    }
    public function badges_overview()
    {
        $userid = backpack_auth()->user()->id;
        $locale = session()->get('locale');
        $name = backpack_auth()->user()->name;
        $score = backpack_auth()->user()->score;

        $badgeData = $this->allbadges();
        extract($badgeData);




        // dd($citymaker);
        return view(
            'badges_overview',
            compact(
                'name',
                'score',
                'explorer',
                'citymaker',
                'architect',
                'flaneur',
                'urbanist',
                'influencer',
                'star',
                'guru',
                'investigator',
                'supermapper',

            )
        );
    }

    public function saveprofile(Request $request)
    {
        $userid = backpack_auth()->user()->id;


        $request->validate([
            'name' => [
                'required',
                Rule::unique('users')->ignore($userid),
            ],
            // other validation rules...
        ]);

        try {
            $infos = Infosperso::where('user_id', $userid)->first();
            if (backpack_auth()->user()->score > 0) {
                backpack_auth()->user()->email = $request->email;
                backpack_auth()
                    ->user()
                    ->save();
                Infosperso::where('user_id', $userid)->update([
                    'email' => $request->email,
                    'age' => $request->age,
                    'gender' => $request->gender,
                    'profession' => $request->profession,
                ]);

                return redirect('/');
            } else {
                backpack_auth()->user()->email = $request->email;
                backpack_auth()
                    ->user()
                    ->save();
                $infos = Infosperso::where('user_id', $userid)->first();
                $infos->user_id = $userid;
                $infos->age = $request->age;
                if ($request->age != null) {
                    backpack_auth()->user()->score =
                        backpack_auth()->user()->score + 1;
                    backpack_auth()
                        ->user()
                        ->save();
                }
                $infos->gender = $request->gender;
                if ($request->gender != null) {
                    backpack_auth()->user()->score =
                        backpack_auth()->user()->score + 1;
                    backpack_auth()
                        ->user()
                        ->save();
                }
                $infos->profession = $request->profession;
                if ($request->profession != null) {
                    backpack_auth()->user()->score =
                        backpack_auth()->user()->score + 1;
                    backpack_auth()
                        ->user()
                        ->save();
                }
                $infos->newuser = 0;
                $infos->save();
                return redirect('/');
            }
        } catch (\Exception $e) {
            // Handle any exceptions that may occur during the update
            return redirect()->back()->with('error', 'Error updating record: ' . $e->getMessage());
        }
    }

    public function savepreferences(Request $request)
    {
        $userid = backpack_auth()->user()->id;
        $infos = Infosperso::where('user_id', $userid)->first();
        $infos->preferences = $request->preferences;
        $infos->save();

        return redirect('/');
    }

    public function newpreference(Request $request)
    {

        $userid = backpack_auth()->user()->id;




        $tag = strtolower($request->preference);
        $preference = Preference::create([
            'user_id' => $userid,
            'name' => $tag,
        ]);
        if ($preference) {
            $infos = Infosperso::where('user_id', $userid)->first();
            $existingPreferences = explode(',', $infos->preferences);
            $existingPreferences = preg_replace('/[^A-Za-z0-9 ]/', '', $existingPreferences);
            $existingPreferences[] = $tag;
            $infos->preferences = $existingPreferences;
            $infos->save();
        }

        return redirect('/preferences');
    }

    public function preferences()
    {
        $userid = backpack_auth()->user()->id;
        $infos = Infosperso::where('user_id', $userid)->first();

        $preferences = explode(',', $infos->preferences);
        $preferences = preg_replace('/[^A-Za-z0-9 ]/', '', $preferences);

        $preferences_array = Preference::getPreferences($userid);


        return view('preferences', compact('preferences', 'preferences_array'));
    }



    public function avatar(Request $request)
    {
        $userid = backpack_auth()->user()->id;
        //dd($request->all());
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg',
        ]);

        $imageName = time() . '.' . $request->image->extension();

        $request->image->storeAs('public/uploads/avatar/', $imageName);

        $user = User::find($userid);

        backpack_auth()->user()->score = backpack_auth()->user()->score + 1;
        backpack_auth()
            ->user()
            ->save();

        $user->avatar = $imageName;
        $user->save();

        return back();
    }

    public function placeDetail($id)
    {

        if ($id) {
            $placeSignle = PlaceDetails::find($id);
            if (!($placeSignle && $placeSignle->user_id == backpack_auth()->user()->id)) {
                return redirect('/');
            }


            return view('placeDetail', compact('placeSignle'));
        }
    }
    public function dashboard()
    {
        $userid = backpack_auth()->user()->id;
        $placeDetails = User::with('placeDetails')->find($userid)->placeDetails()->orderBy('id', 'desc')->paginate(10);

        $score = backpack_auth()->user()->score;


        return view('dashboard', compact('placeDetails', 'score'));
    }
    public function loadMore_dashboard(Request $request)
    {
        $userid = backpack_auth()->user()->id;
        $page = $request->get('page');

        $placeDetails = User::with('placeDetails')->find($userid)->placeDetails()->orderBy('id', 'desc')->paginate(10, ['*'], 'page', $page);

        $html = view('item_dashboard', compact('placeDetails'))->render();

        return response()->json(['html' => $html, 'hasMorePages' => $placeDetails->hasMorePages()]);
    }







    static function pages()
    {
        $pages = Pages::all();
        return $pages;
    }

    static function allusers()
    {
        $users = User::all();
        return $users;
    }

    static function infosperso()
    {
        $infos = Infosperso::all();
        return $infos;
    }

    static function myprofile()
    {
        $userid = backpack_auth()->user()->id;
        $infos = Infosperso::where('user_id', $userid)->first();
        return $infos;
    }


    //----------------------new code----------------------


    function addNewPlaceData($postData)
    {

        $result_array = [];
        if (isset($postData->place_name) && !empty($postData->place_name)) {


            $check = Place::where('name', $postData->place_name)->exists();

            if ($check) {

                return false;
            }



            $place = Place::create([
                'name' => $postData->place_name,
                'user_id' => backpack_user()->id,
            ]);
            $result_array['place_id'] = $place->id;
        }
        if (isset($postData->observation_name) && !empty($postData->observation_name)) {

            $check = Observation::where('name', $postData->observation_name)->exists();

            if ($check) {

                return false;
            }

            $observation = Observation::create([
                'name' => $postData->observation_name,
                'user_id' => backpack_user()->id,
            ]);
            $result_array['observations'][] = array(
                'observation_id' => $observation->id,
                'child_observation_id' => NULL,
                'feeling_id' => $postData->feeling_id ? $postData->feeling_id : NULL,
            );
        }

        return (object)$result_array;
    }

    public function addMapPlace(Request $request, $id = null)
    {

        $postData = json_decode($request->place_data, true);
        $postData = (object)$postData;
        $returnData = $this->addNewPlaceData($postData);

        if ($returnData == false) {
            $response['status'] = 'error';
            $response['msg'] = 'Place or observation already exist!';
            return response()->json($response);
        }
        $postData = (object)array_merge((array)$postData, (array)$returnData);


        //    dd($postData);

        $userId = backpack_auth()->user()->id;
        $response = ['status' => '', 'msg' => '', 'place_detail_id' => '', 'tab' => '', 'completed' => false];

        if (isset($postData->place_detail_id) && $postData->place_detail_id != '') {
            $place_detail = PlaceDetails::find($postData->place_detail_id);
        } else {
            $latitude = $postData->latitude;
            $longitude = $postData->longitude;
            $radius = 100; // Meters
            $place_detail = PlaceDetails::selectRaw("*,
                    ( 6371 * acos( cos( radians(?) )
                    * cos( radians( latitude ) )
                    * cos( radians( longitude ) - radians(?)) + sin( radians(?) )
                    * sin( radians( latitude ) ) )) AS distance", [$latitude, $longitude, $latitude])
                ->having("distance", "<", ($radius / 1000))
                ->where('user_id', $userId)
                ->first();
        }




        if (isset($place_detail)) {

            $place_detail->update([
                'user_id' =>  backpack_auth()->user()->id,
                'place_description' =>  $postData->place_description ? $postData->place_description : NULL,
                'obsevation_description' =>  $postData->observation_description ? $postData->observation_description : NULL,
                // 'latitude' => $postData->latitude,
                // 'longitude' => $postData->longitude,
            ]);

            if (isset($postData->update) && $postData->update == 'place') {
                $place_detail->updatePlaces($place_detail, $postData);
            } else if (isset($postData->update) && $postData->update == 'observation') {
                $place_detail->updateObservations($place_detail, $postData);
            } else {

                dd('here');
                // $place_detail->updateMethod($place_detail,$postData);
            }


            $response['status'] = 'success';
            $response['msg'] = 'data updated successfully!';
        } else {
            $place_detail = PlaceDetails::create([
                'user_id' =>  backpack_auth()->user()->id,
                'place_description' =>  $postData->place_description ? $postData->place_description : NULL,
                'obsevation_description' =>  $postData->observation_description ? $postData->observation_description : NULL,
                'latitude' => $postData->latitude,
                'longitude' => $postData->longitude,
            ]);

            if ($postData->place_id) {
                PlaceDetailPlace::create([
                    'place_detail_id' => $place_detail->id,
                    'place_id' => $postData->place_id,
                    'place_child_id' => $postData->child_place_id ? $postData->child_place_id : NULL,
                ]);
            }

            if (isset($postData->observations) && is_array($postData->observations) && count($postData->observations) > 0) {
                foreach ($postData->observations as $obsrv) {
                    PlaceDetailObservation::create([
                        'place_detail_id' => $place_detail->id,
                        'observation_id' => $obsrv['observation_id'],
                        'observation_child_id' => $obsrv['child_observation_id'] ? $obsrv['child_observation_id'] : NULL,
                        'feeling_id' => $obsrv['feeling_id'],
                    ]);
                }
            }



            backpack_auth()->user()->incrementScore(1);
            $response['status'] = 'success';
            $response['msg'] = 'data added successfully!';
        }

        if ($request->hasFile('place_image')) {
            $request->validate([
                'place_image' => 'required|image|mimes:jpeg,png,jpg,gif',
            ]);
            $imageName = time() . '_place.' . $request->place_image->extension();
            $request->place_image->storeAs('public/uploads/place/', $imageName);

            $place_detail->update([
                'place_image' =>  $imageName,
            ]);
        }

        if ($request->hasFile('observation_image')) {
            $request->validate([
                'observation_image' => 'required|image|mimes:jpeg,png,jpg,gif',
            ]);
            $imageName = time() . '_observation.' . $request->observation_image->extension();
            $request->observation_image->storeAs('public/uploads/observation/', $imageName);
            $place_detail->update([
                'obsevation_image' =>  $imageName,
            ]);
        }



        $response['place_detail_id'] = $place_detail->id;


        if ((isset($place_detail->placeDetail) && $place_detail->placeDetail->id) && (isset($place_detail->observationsDetail) &&  count($place_detail->observationsDetail)) > 0) {
            $response['completed'] = true;
        }

        $response['tab'] = $postData->tab;

        $response['place_id'] = $place_detail->placeDetail->place_id ?? null;

        return response()->json($response);
    }


    public function addNewPlace(Request $request)
    {



        if ($request->place_name) {
            $place = Place::create([
                'name' => $request->place_name,
                'user_id' => backpack_user()->id,
            ]);
        }

        if ($request->observation_name) {
            $observation = Observation::create([
                'name' => $request->observation_name,
                'user_id' => backpack_user()->id,
            ]);
        }


        PlaceDetails::create([
            'place_id' => $place->id ?? NULL,
            'observation_id' => $observation->id ?? NULL,
            'user_id' =>  backpack_auth()->user()->id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,

        ]);

        if ($request->observation_name) {
            return response()->json([
                'status' => 'success',
                'msg' => 'Observation added successfully, You can also add place for this place!',

            ]);
        } else {
            return response()->json([
                'status' => 'success',
                'msg' => 'Place added successfully, You also add obervation for this place!',

            ]);
        }
    }


    public function createNew($type = 'place', $edit_id = null)
    {
        $only=false;
        if(request()->only){
            $only=true;
        }

        if ($edit_id) {
            $checkplace = PlaceDetails::find($edit_id);
            if (!($checkplace && $checkplace->user_id == backpack_auth()->user()->id)) {
                return redirect('/');
            }
        }

        $allPlaces = Place::where('user_id', null)
            ->where('parent_id', NULL)
            ->orWhere('user_id', backpack_auth()->user()->id)
            ->get();



        $allObservations = Observation::where('user_id', null)
            ->where('parent_id', NULL)
            ->orWhere('user_id', backpack_auth()->user()->id)
            ->get();

        $feelings = Feeling::all();

        return view('add-new-place', compact('allObservations', 'allPlaces', 'type', 'feelings', 'edit_id','only'));
    }



    public function filter()
    {

        return view('filter');
    }


    public function saveDes(Request $request)
    {

        PlaceDetails::where('id', $request->id)
            ->update([

                'description' => $request->data

            ]);
    }
    public function saveComment(Request $request)
    {
        PlaceComment::updateOrcreate(
            [
                'place_detail_id' => $request->id,
                'user_id' => backpack_auth()->user()->id,
            ],
            [
                'comment' => $request->data,
            ]
        );
    }

    public function setLike(Request $request)
    {

        $placeLike = PlaceLike::where('place_detail_id', $request->id)
            ->where('user_id', backpack_auth()->user()->id)
            ->first();

        if ($placeLike) {
            $placeLike->delete();
        } else {
            PlaceLike::create([
                'place_detail_id' => $request->id,
                'user_id' => backpack_auth()->user()->id,
            ]);
        }
    }

    public function truncate()
    {
        // DB::table('place_likes')->delete();
        // DB::table('place_comments')->delete();
        // DB::table('place_detail_places')->delete();
        // DB::table('place_detail_observations')->delete();
        // DB::table('place_details')->delete();

    }
}
