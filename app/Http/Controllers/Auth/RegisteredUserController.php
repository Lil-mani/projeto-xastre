<?php

namespace App\Http\Controllers\Auth;
use Validator;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Userdata;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'cpf' => ['required', 'string', 'max:14'],
            'telefone' => ['required', 'string', 'max:15'],
            'dob' => ['required', 'date'],
            'cep' => ['required', 'string', 'max:9'],
            'logradouro' => ['nullable', 'string', 'max:255'],
            'complemento' => ['nullable', 'string', 'max:255'],
            'bairro' => ['nullable', 'string', 'max:255'],
            'localidade' => ['nullable', 'string', 'max:255'],
            'uf' => ['nullable', 'string', 'max:2'],
            'role' => ['required', 'string', 'max:255'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $userdata = Userdata::create([
            'name' => $request->name,
            'email' => $request->email,
            'password'=> $request->password,
            'cpf' => $request->cpf,
            'telefone' => $request->telefone,
            'dob' => $request->dob,
            'cep' => $request->cep,
            'logradouro' => $request->logradouro,
            'bairro' => $request->bairro,
            'localidade' => $request->localidade,
            'uf' => $request->uf,
            'role' => $request->role,
        ]);

        event(new Registered($user));

        $user->save();
        //Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
    public function store_no(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'cpf' => ['required', 'string', 'max:14'],
            'telefone' => ['required', 'string', 'max:15'],
            'dob' => ['required', 'date'],
            'cep' => ['required', 'string', 'max:9'],
            'logradouro' => ['nullable', 'string', 'max:255'],
            'complemento' => ['nullable', 'string', 'max:255'],
            'bairro' => ['nullable', 'string', 'max:255'],
            'localidade' => ['nullable', 'string', 'max:255'],
            'uf' => ['nullable', 'string', 'max:2'],
            'role' => ['required', 'string', 'max:255'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $userdata = Userdata::create([
            'name' => $request->name,
            'email' => $request->email,
            'password'=> $request->password,
            'cpf' => $request->cpf,
            'telefone' => $request->telefone,
            'dob' => $request->dob,
            'cep' => $request->cep,
            'logradouro' => $request->logradouro,
            'bairro' => $request->bairro,
            'localidade' => $request->localidade,
            'uf' => $request->uf,
            'role' => $request->role,
        ]);

        event(new Registered($user));

        $user->save();
        //Auth::login($user);
    }
    public function getPsicologos()
    {
        $names = Userdata::where('role','psicologo')->pluck('id','name');
        return response()->json($names);
    }
    public function show() {
        $users = Userdata::get();
        return response()->json($users);    
    }
}
