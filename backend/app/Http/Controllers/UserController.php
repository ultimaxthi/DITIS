<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService){
        $this->userService = $userService;
    }

    public function index()
    {
        return response()->json($this->userService->getAllUsers());
    }

    public function show($id)
    {
        try
        {
            $user = $this->userService->getUserbyId($id);
            if(!$user){
                return response()->json(['message'=>'Usuário não encontrado'],404);
            }
            return response()->json($user);
        } catch (\Exception $e) 
        {
            return response()->json(['error'=> $e->getMessage()],500);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'cpf'       => 'required|string|unique:users,cpf',
            'password'  => 'required|string|min:6',
            'role'      => 'required|in:user,admin',
        ]);

        try
        {
            $user = $this->userService->addAdmin($validated);
            return response()->json($user,201);
        } catch (\Exception $e){
            return response()->json(['error'=> $e->getMessage()],403);
        }
    }

    public function updateWithRole(Request $request, $id)
    {
        try {
            $data = $request->validate([
                'name' => 'sometimes|required|string',
                'email' => 'sometimes|required|string|email|unique:users,email,' . $id,
                'cpf' => 'sometimes|required|string|max:11|unique:users,cpf,' . $id,
                'password' => 'sometimes|required|string|min:8|confirmed|nullable',
                'role' => 'sometimes|string|in:admin,user',
            ]);

            $user = $this->userService->updateAdmin($id, $data);

            return response()->json([
                'message' => ucfirst($user->role) . ' atualizado com sucesso!',
                'user' => $user,
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name'      => 'sometimes|string|max:255',
            'email'     => 'sometimes|email|unique:users,email,' . $id,
            'cpf'       => 'sometimes|string|unique:users,cpf,' . $id,
            'password'  => 'nullable|string|min:6',
            'role'      => 'sometimes|in:user,admin',
        ]);

        try
        {
            $isAdmin = $this->userService->isCurrentUserAdmin();
            $user = $this->userService->updateUser($id,$validated,$isAdmin);
            return response()->json($user);
        } catch (\Exception $e) {
            return response()->json(['error'=> $e->getMessage()],403);
        }
    }

    public function destroy($id)
    {
        try{
            $this->userService->deleteUser($id);
            return response()->json(['message'=>'Usuário excluído com sucesso.']);
        } catch (\Exception $e)
        {
            return response()->json(['error'=> $e->getMessage()], 403);
        }
    }

    public function summary()
    {
        return response()->json($this->userService->getSummaryData());
    }

}

?>