<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\Responsibility;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;    
use App\Http\Requests\CreateResponsibilityRequest;

class ResponsibilityController extends Controller
{

    public function fetch(Request $request){
        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);

        $responsibilityQuery = Responsibility::query();

        if($id){
            $responsibility = $responsibilityQuery->find($id);
            if($responsibility){
                return ResponseFormatter::success($responsibility,'Responsibility Found');
            }
            return ResponseFormatter::error('Responsibility Not Found');
        }

        $responsibilities = $responsibilityQuery->where('role_id', $request->role_id);
        if($name){
            $responsibilities->where('name', 'like', '%' . $name . '%');
        }
        return ResponseFormatter::success(
            $responsibilities->paginate($limit),
            'Responsibilities Found'
        );
    }

    public function create(CreateResponsibilityRequest $request){
        try{
            // Create Responsibility
            $responsibility = Responsibility::create([
                'name' => $request->name,
                'role_id' => $request->role_id
            ]);
            if(!$responsibility){
                throw new Exception('Responsibility not created');
            }
            return ResponseFormatter::success($responsibility, 'Responsibility Created');
        }
        catch(Exception $error){
            return ResponseFormatter::error($error->getMessage(),500);
        }
    }


    public function destroy($id){
        try{
            $responsibility = Responsibility::find($id);
            if(!$responsibility){
                return ResponseFormatter::error('Responsibility Not Found', 404);
            }
            $responsibility->delete();
            return ResponseFormatter::success($responsibility, 'Responsibility Deleted');
        }
        catch(Exception $error){
            return ResponseFormatter::error($error->getMessage(),500);
        }
    }
}
