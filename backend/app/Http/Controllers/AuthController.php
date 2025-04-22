<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserService;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function register(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string',
                'email' => 'required|string|email|unique:users',
                'cpf' => ['required', 'string', 'unique:users,cpf', new \App\Rules\Cpf],
                'password' => 'required|string|min:8|confirmed',
            ]);

            // Define a role com base no status do usuário autenticado
            $isAdmin = $this->userService->isCurrentUserAdmin();
            $data['role'] = $isAdmin ? $request->input('role', 'user') : 'user';

            $user = $this->userService->registerUser($data);
            
            return response()->json([
                'message' => 'Usuário registrado com sucesso!', 
                'user' => $user
            ], 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $data = $request->validate([
                'cpf' => 'required|string|regex:/^\d{11}$/',
                'password' => 'required|string',
            ]);

            $loginData = $this->userService->loginUser($data['cpf'], $data['password']);
            return response()->json($loginData);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 401);
        }
    }


    public function logout(Request $request)
    {
        try {
            $result = $this->userService->logoutUser($request->user());
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function forgotPassword(Request $request)
    {
        try {
            $request->validate(['email' => 'required|email']);

            $status = $this->userService->sendResetLink($request->email);

            return $status === Password::RESET_LINK_SENT
                ? response()->json(['message' => __($status)])
                : response()->json(['message' => __($status)], 400);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function reset(Request $request)
    {
        try {
            $request->validate([
                'token' => 'required|string',
                'email' => 'required|email',
                'password' => 'required|string|confirmed',
            ]);

            $this->userService->resetPassword($request->email, $request->token, $request->password);
            
            return response()->json(['message' => 'Senha atualizada com sucesso.']);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
