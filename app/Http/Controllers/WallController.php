<?php

namespace App\Http\Controllers;

use App\Models\Wall;
use App\Models\WallLike;
use Illuminate\Http\Request;

class WallController extends Controller
{
    public function getAll()
    {
        $array = ['error' => '', 'list' => []]; //se não tiver itens, retorna uma lista vazia

        $user = auth()->user(); //pegando o usuario logado

        $walls = Wall::all(); // pegando todos os avisos do mural

        foreach ($walls as $wallKey => $wallValue) {
            $walls[$wallKey]['likes'] = 0; //criando campo likes
            $walls[$wallKey]['liked'] = false; //criando campo liked

            $likes = WallLike::where('id_wall', $wallValue['id'])->count(); //quantos likes tenho em determinada postagem
            $walls[$wallKey]['likes'] = $likes;

            $meLikes = WallLike::where('id_wall', $wallValue['id'])
                ->where('id_user', $user['id'])
                ->count();

            if ($meLikes > 0) {
                $walls[$wallKey]['liked'] = true;
            }
        }
        $array['list'] = $walls;

        return $array;
    }

    public function like($id)
    {
        $array = ['error' => ''];

        $user = auth()->user();

        $meLikes = WallLike::where('id_wall', $id)
            ->where('id_user', $user['id'])
            ->count();

        if ($meLikes > 0) {
            //remover o like
            WallLike::where('id_wall', $id)
                ->where('id_user', $user['id'])
                ->delete();
            $array['liked'] = false;
        } else {
            //adicionar o like
            $newLike = new WallLike();
            $newLike->id_wall = $id;
            $newLike->id_user = $user['id'];
            $newLike->save();

            $array['liked'] = true;
        }

        $array['likes'] = WallLike::where('id_wall', $id)->count();

        return $array;
    }
}
