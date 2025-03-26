<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CreateRoleRequest;
use App\Http\Requests\UpdateRoleRequest;

class RoleController extends Controller
{

    public function fetch(Request $request){
        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);

        $roleQuery = Role::query();

        if($id){
            $role = $roleQuery->find($id);
            if($role){
                return ResponseFormatter::success($role,'Role Found');
            }
            return ResponseFormatter::error('Role Not Found');
        }

        $roles = $roleQuery->where('company_id', $request->company_id);
        if($name){
            $roles->where('name', 'like', '%' . $name . '%');
        }
        return ResponseFormatter::success(
            $roles->paginate($limit),
            'Roles Found'
        );
    }

    public function create(CreateRoleRequest $request){
        try{
            // Create Role
            $role = Role::create([
                'name' => $request->name,
                'company_id' => $request->company_id
            ]);
            if(!$role){
                throw new Exception('Role not created');
            }
            return ResponseFormatter::success($role, 'Role Created');
        }
        catch(Exception $error){
            return ResponseFormatter::error($error->getMessage(),500);
        }
    }

    public function update(UpdateRoleRequest $request, $id){
        try{
            $role = Role::find($id);
            if(!$role){
                return ResponseFormatter::error('Role Not Found', 404);
            }
            // Update Role
            $role->update([
                'name' => $request->name,
                'company_id' => $request->company_id
            ]);

            return ResponseFormatter::success($role, 'Role Updated');
        }
        catch(Exception $error){
            return ResponseFormatter::error($error->getMessage(),500);
        }
    }

    public function destroy($id){
        try{
            $role = Role::find($id);
            if(!$role){
                return ResponseFormatter::error('Role Not Found', 404);
            }
            $role->delete();
            return ResponseFormatter::success($role, 'Role Deleted');
        }
        catch(Exception $error){
            return ResponseFormatter::error($error->getMessage(),500);
        }
    }
}
