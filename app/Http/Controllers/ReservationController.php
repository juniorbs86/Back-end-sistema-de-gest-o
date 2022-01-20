<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Unit;
use Illuminate\Http\Request;
use App\Models\AreaDisabledDay;
use App\Models\Reservation;
use Illuminate\Support\Facades\Validator;

class ReservationController extends Controller
{
    public function getReservations()
    {
        $array = ['error' => ''];
        $daysHelper = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];

        $areas = Area::where('allowed', 1)->get();

        foreach ($areas as $area) {
            $dayList = explode(',', $area['days']);

            $dayGroups = [];
            //adicionando o primeiro dia
            $lastDay = intval(current($dayList));
            $dayGroups[] = $daysHelper[$lastDay];
            array_shift($dayList);
            //adicionando dias relevantes
            foreach ($dayList as $day) {
                if (intval($day) != $lastDay + 1) {
                    $dayGroups[] = $daysHelper[$lastDay];
                    $dayGroups[] = $daysHelper[$day];
                }
                $lastDay = intval($day);
            }

            //adicionando o ultimo dia

            $dayGroups[] = $daysHelper[end($dayList)];

            //juntando as datas (dia1-dia2)
            $dates = '';
            $close = 0;
            foreach ($dayGroups as $group) {
                if ($close === 0) {
                    $dates .= $group;
                } else {
                    $dates .= '-' . $group . ',';
                }
                $close = 1 - $close;
            }

            $dates = explode(',', $dates);
            array_pop($dates);

            //adicionando o TIME
            $start = date('H:i', strtotime($area['start_time']));
            $end = date('H:i', strtotime($area['end_time']));

            foreach ($dates as $dKey => $dValue) {
                $dates[$dKey] .= ' ' . $start . ' às ' . $end;
            }

            $array['list'][] = [
                'id' => $area['id'],
                'cover' => asset('storage/' . $area['cover']),
                'title' => $area['title'],
                'dates' => $dates
            ];
        }

        return $array;
    }

    public function setReservation($id, Request $request)
    {
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'date' => 'required|date_format:Y-m-d',
            'time' => 'required|date_format:H:i:s',
            'property' => 'required'
        ]);
        if (!$validator->fails()) {
            $date = $request->input('date');
            $time = $request->input('time');
            $property = $request->input('property');

            $unit = Unit::find($property);
            $area = Area::find($id);



            if ($unit && $area) {
                $can = true;

                $weekDay = date('w', strtotime($date));

                //verificar se esta dentro da disponibilidade padrao
                $allowedDays = explode(',', $area['days']);
                if (!in_array($weekDay, $allowedDays)) {
                    $can = false;
                } else {
                    $start = strtotime($area['start_time']);
                    $end = strtotime('-1 hour', strtotime($area['end_time']));
                    $revtime = strtotime($time);
                    if ($revtime < $start || $revtime > $end) {
                        $can = false;
                    }
                }


                //verificar se esta fora dos disabledDays
                $existingDisabledDay = AreaDisabledDay::where('id_area', $id)
                    ->where('day', $date)
                    ->count();
                if ($existingDisabledDay > 0) {
                    $can = false;
                }

                //verificar se nao existe outra reserva no mesmo dia/hora
                $existingReservations = Reservation::where('id_area', $id)
                    ->where('reservation_date', $date . ' ' . $time)
                    ->count();
                if ($existingReservations > 0) {
                    $can = false;
                }


                if ($can) {
                    $newReservation = new Reservation();
                    $newReservation->id_unit = $property;
                    $newReservation->id_area = $id;
                    $newReservation->reservation_date = $date . ' ' . $time;
                    $newReservation->save();
                } else {
                    $array['error'] = 'Reserva não permitida neste dia/horario';
                    return $array;
                }
            } else {
                $arra['error'] = 'Dados incorretos';
                return $array;
            }
        } else {
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        return $array;
    }

    public function getDisabledDates($id)
    {
        $array = ['error' => ''];


        return $array;
    }
}