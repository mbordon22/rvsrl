<?php

namespace App\Repositories\Admin;

use App\Http\Requests\Admin\UpdateUserRequest;
use Exception;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Prettus\Repository\Eloquent\BaseRepository;
use Carbon\Carbon;

class UserRepository extends BaseRepository
{
    protected $role;

    function model()
    {
        $this->role = new Role();
        return User::class;
    }

    public function index($userTable)
    {
        if (request()['action']) {
            return redirect()->back();
        }

        return view('admin.user.index', ['tableConfig' => $userTable]);
    }

    public function store($request)
    {
        DB::beginTransaction();

        try {
            $admissionDate = Carbon::createFromFormat('d/m/Y', $request->admission_date)->format('Y-m-d');

            $user = $this->model->create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => (string) $request->phone,
                'status' => $request->status,
                'dob' => $request->dob,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'state_id' => $request->state_id,
                'location' => $request->location,
                'postal_code' => $request->postal_code,
                'about_me' => $request->about_me,
                'admission_date' => $admissionDate,
                'dni' => $request->dni,
                'telecom_id' => $request->telecom_id
            ]);

            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $user->addMediaFromRequest('image')->toMediaCollection('image');
            }

            if ($request->role_id) {
                $role = $this->role->findOrFail($request->role_id);
                $user->assignRole($role);
            }
            
            DB::commit();

            return redirect()->route('admin.user.index')->with('success', __('Usuario Creado Correctamente'));

        } catch (Exception $e){

            DB::rollback();

            throw $e;
        }
    }

    public function update($request, $id)
    {
        DB::beginTransaction();

        try {
            $admissionDate = Carbon::createFromFormat('d/m/Y', $request['admission_date'])->format('Y-m-d');

            $user = $this->model->findOrFail($id);
            $user->update([
                'first_name' => $request['first_name'],
                'last_name' => $request['last_name'],
                'email' => $request['email'],
                'phone' => (string) $request['phone'],
                'postal_code' => $request['postal_code'],
                'dob' => $request['dob'],
                'dni' => $request['dni'],
                'admission_date' => $admissionDate,
                'password' => Hash::make($request['password']),
                'status' => $request['status'],
                'state_id' => $request['state_id'],
                'about_me' => $request['about_me'],
                'telecom_id' => $request['telecom_id'],
                'location' => $request['location'],
            ]);

            if($request['password']) {
                $user->update(['password' => Hash::make($request['password'])]);
            }

            $user = $this->model->findOrFail($id);
            if ($user->system_reserve) {
                return redirect()->back()->with('error', __('Este usuario no puede ser editado, ya que es un usuario de sistema'));
            }
            
            if (isset($request['role_id'])) {
                $role = $this->role->find($request['role_id']);
                $user->syncRoles($role);
            }

            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $user->clearMediaCollection('image');
                $user->addMediaFromRequest('image')->toMediaCollection('image');
            }

            DB::commit();
            return redirect()->route('admin.user.index')->with('success', __('Usuario Actualizado Correctamente'));

        } catch (Exception $e) {

            DB::rollback();

            throw $e;
        }
    }

    public function status($id, $status)
    {
        try {

            $user = $this->model->findOrFail($id);
            $user->update(['status' => $status]);

            return json_encode(["resp" => $user]);

        } catch (Exception $e) {

            throw $e;
        }
    }

    public function destroy($id)
    {
        try {

            $user = $this->model->findOrFail($id);
            $user->destroy($id);
            return redirect()->back()->with('success', __('Usuario Eliminado Correctamente'));

        } catch (Exception $e) {

            throw $e;
        }
    }
}