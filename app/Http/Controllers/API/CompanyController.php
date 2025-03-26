<?php

namespace App\Http\Controllers\API;
use Exception;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CreateCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Models\User;

class CompanyController extends Controller
{
    public function fetch(Request $request){
        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);

        if($id){
            $company = Company::whereHas('users', function ($query){
                $query->where('user_id', Auth::id());
            })->with(['users'])->find($id);
            if($company){
                return ResponseFormatter::success($company);
            }
            return ResponseFormatter::error('Company Not Found');
        }

        $companies = Company::with(['users'])->whereHas('users', function($query){
            $query->where('user_id', Auth::id());
        });
        if($name){
            $companies->where('name', 'like', '%' . $name . '%');
        }
        return ResponseFormatter::success(
            $companies->paginate($limit),
            'Companies Found'
        );
    }

    public function create(CreateCompanyRequest $request){
        try{
            // Upload Logo
            if($request->hasFile('logo')){
                $path = $request->file('logo')->store('public/logos');
            }
            // Create Company
            $company = Company::create([
                'name' => $request->name,
                'logo' => isset($path) ? $path : null,
            ]);
            if(!$company){
                throw new Exception('Company not created');
            }
            // Attach User to Company
            $user = User::find(Auth::id());
            $user->companies()->attach($company->id);

            // Load Users at Company
            $company->load('users');

            return ResponseFormatter::success($company, 'Company Created');
        }
        catch(Exception $error){
            return ResponseFormatter::error($error->getMessage(),500);
        }
    }

    public function update(UpdateCompanyRequest $request, $id){
        try{
            $company = Company::find($id);
            if(!$company){
                return ResponseFormatter::error('Company Not Found', 404);
            }
            // Upload Logo
            if($request->hasFile('logo')){
                $path = $request->file('logo')->store('public/logos');
                $company->logo = $path;
            }
            // Update Company
            $company->name = $request->name;
            $company->save();

            return ResponseFormatter::success($company, 'Company Updated');
        }
        catch(Exception $error){
            return ResponseFormatter::error($error->getMessage(),500);
        }

    }

}
