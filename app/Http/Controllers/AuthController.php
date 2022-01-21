<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\User;

use Illuminate\Http\Request;
use illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function unauthorized()
    {
        return response()->json([
            'error' => 'Não autorizado'
        ], 401);
    }

    public function register(Request $request) //receber os dados a serem enviados
    {
        $array = ['error' => '']; //receber os dados para poder ser validado

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email', //tabela users, campo email
            'cpf' => 'required|digits:11|unique:users,cpf',
            'password' => 'required',
            'password_confirm' => 'required|same:password'
        ]);
        //verificar se teve algum erro ou falha
        if (!$validator->fails()) { // se deu certo entra nos campos name,email,cpf...
            $name = $request->input('name');
            $email = $request->input('email');
            $cpf = $request->input('cpf');
            $password = $request->input('password');

            $hash = password_hash($password, PASSWORD_DEFAULT);

            //criando o usuario...
            $newUser = new User();
            $newUser->name = $name;
            $newUser->email = $email;
            $newUser->cpf = $cpf;
            $newUser->password = $hash;
            $newUser->save();

            $token = auth()->attempt([ //gerando o token
                'cpf' => $cpf,
                'password' => $password
            ]);

            if (!$token) { //se nao gerou o token...
                $array['error'] = 'Ocorreu um erro.';
                return $array;
            }

            $array['token'] = $token; //token no array de resposta

            $user = auth()->user(); //pegando os dados do usuario
            $array['user'] = $user;

            $properties = Unit::select(['id', 'name']) //pegando as propriedades desse usuario
                ->where('id_owner', $user['id'])
                ->get();

            $array['user']['properties'] = $properties; //adicionando as propriedades
        } else {
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        return $array;
    }

    public function login(Request $request)
    {
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [ //validando cpf e senha
            'cpf' => 'required|digits:11',
            'password' => 'required'
        ]);
        if (!$validator->fails()) { //se não houve erros, continua o processo de login
            $cpf = $request->input('cpf');
            $password = $request->input('password');

            $token = auth()->attempt([ //gerando o token
                'cpf' => $cpf,
                'password' => $password
            ]);

            if (!$token) {
                $array['error'] = 'CPF e/ou SENHA estão errados';
                return $array;
            }

            $array['token'] = $token; //token no array de resposta

            $user = auth()->user(); //pegando os dados do usuario
            $array['user'] = $user;

            $properties = Unit::select(['id', 'name']) //pegando as propriedades desse usuario
                ->where('id_owner', $user['id'])
                ->get();

            $array['user']['properties'] = $properties; //adicionando as propriedades
        } else {
            $array['error'] = $validator->errors()->first();
            return $array;
        }


        return $array;
    }

    public function validateToken()
    {
        $array = ['error' => ''];
        $user = auth()->user(); //pegando os dados do usuario
        $array['user'] = $user;

        $properties = Unit::select(['id', 'name']) //pegando as propriedades desse usuario
            ->where('id_owner', $user['id'])
            ->get();

        $array['user']['properties'] = $properties; //adicionando as propriedades

        return $array;
    }

    public function logout()
    {
        $array = ['error' => ''];
        auth()->logout();
        return $array;
    }
}
