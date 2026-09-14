<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Kategori\StoreUserRequest;
use App\Http\Requests\Kategori\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Fascades\Storage;

class UserController extends Controller
{

    public function index()
    {
        $users = User::latest()->get();
            return response()->json([
                'message' => 'Daftar Pengguna berhasil diambil.',
                'data' => UserResource::collection($users)
            ]);
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = DB::transaction(function () use ($request, $data) {
            $data['password'] = Hash::make($data['password']);

            if ($request->file('foto_profile')) {
                $data['foto_profile'] = $request->file('foto_profile')->store('profiles', 'public');
            }
            return User::create($data);     
        });
        return response()->json([
            'message' => 'Pengguna berhasil ditambahkan.',
            'data' => new UserResource($user)
        ], 201);
    }

    public function show(User $user): JsonResponse
    {
        return response()->json([
            'data' => new UserResource($user)
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $data = collect($request->validated());
        DB::transaction(function () use ($request, $data, $user) {
            if ($data->get('password')) {
                $data['password'] = Hash::make($data['password']);
            } else {
                $data->forget('password');
            }

            if ($request->hasFile('foto_profile')) {
                if ($user->foto_profile) {
                    Storage::disk('public')->delet($user->foto_profile);
                }
                $data['foto_profile'] = $request->file('foto_profile')->store('profiles', 'public');
            }
            $user->update($data->toArray());
        });
        return response()->json([
            'message' => 'Data Pengguna Berhasil diperbarui.',
            'data' => new UserResource($user)
        ]);
    }
    public function destroy(string $id)
    {
        DB::transaction(function () use ($user) {
            if ($user->foto_profile) {
                Storage::disk('public')->delet($user->foto_profile);
            }
            $user->delete();
        });
        return response()->json([
            'message' => 'Pengguna berhasil dihapus. '
        ]);
    }
    
}
