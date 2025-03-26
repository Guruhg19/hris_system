<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\Team;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CreateTeamRequest;
use App\Http\Requests\UpdateTeamRequest;

class TeamController extends Controller
{

    public function fetch(Request $request){
        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);

        $teamQuery = Team::query();

        if($id){
            $team = $teamQuery->find($id);
            if($team){
                return ResponseFormatter::success($team,'Team Found');
            }
            return ResponseFormatter::error('Team Not Found');
        }

        $teams = $teamQuery->where('company_id', $request->company_id);
        if($name){
            $teams->where('name', 'like', '%' . $name . '%');
        }
        return ResponseFormatter::success(
            $teams->paginate($limit),
            'Teams Found'
        );
    }

    public function create(CreateTeamRequest $request){
        try{
            // Upload icon
            if($request->hasFile('icon')){
                $path = $request->file('icon')->store('public/icons');
            }
            // Create Team
            $team = Team::create([
                'name' => $request->name,
                'icon' => $path,
                'company_id' => $request->company_id
            ]);
            if(!$team){
                throw new Exception('Team not created');
            }
            return ResponseFormatter::success($team, 'Team Created');
        }
        catch(Exception $error){
            return ResponseFormatter::error($error->getMessage(),500);
        }
    }

    public function update(UpdateTeamRequest $request, $id){
        try{
            $team = Team::find($id);
            if(!$team){
                return ResponseFormatter::error('Team Not Found', 404);
            }
            // Upload Icon
            if($request->hasFile('icon')){
                $path = $request->file('icon')->store('public/icons');
                // $team->logo = $path;
            }
            // Update Team
            $team->update([
                'name' => $request->name,
                'icon' => isset($path) ? $path : $team->icon,
                'company_id' => $request->company_id
            ]);

            return ResponseFormatter::success($team, 'Team Updated');
        }
        catch(Exception $error){
            return ResponseFormatter::error($error->getMessage(),500);
        }
    }

    public function destroy($id){
        try{
            $team = Team::find($id);
            if(!$team){
                return ResponseFormatter::error('Team Not Found', 404);
            }
            $team->delete();
            return ResponseFormatter::success($team, 'Team Deleted');
        }
        catch(Exception $error){
            return ResponseFormatter::error($error->getMessage(),500);
        }
    }
}
