<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        // Leer el archivo JSON 
        $reservationsData = file_get_contents(storage_path('app/json/reservations.json'));
        $reservationsArray = json_decode($reservationsData, true);
        $routesData = file_get_contents(storage_path('app/json/route_data.json'));
        $routesArray = json_decode($routesData, true);
        $disabledDaysData = file_get_contents(storage_path('app/json/calendar_days_disabled.json'));
        $disabledDaysArray = json_decode($disabledDaysData, true);
        $servicesData = file_get_contents(storage_path('app/json/services.json'));
        $servicesArray = json_decode($servicesData, true);

        // Filtro por Día (DD-MM-YYYY)
        $dayFilter = $request->input('day');
        if ($dayFilter) {
            $filteredDataDay = [];
            foreach ($reservationsArray['reservations'] as $reservation) {
                $reservationStart = $reservation['reservation_start'];
                $reservationStartDate = date('d-m-Y', strtotime($reservationStart));

                if ($reservationStartDate == $dayFilter) {
                    $filteredDataDay[] = $reservation;
                }
            }
            return response()->json($filteredDataDay);
        }

        // Filtro por Rango (YYYY-MM-DD / YYYY-MM-DD)
        $rangeFilterStart = $request->input('range_start');
        $rangeFilterEnd = $request->input('range_end');
        if ($rangeFilterStart && $rangeFilterEnd) {
            $filteredDataRange = [];
            foreach ($reservationsArray['reservations'] as $reservation) {
                $reservationStart = $reservation['reservation_start'];
                $reservationEnd = $reservation['reservation_end'];

                // Ajustar el formato de las fechas
                $reservationStartDate = date('Y-m-d', strtotime($reservationStart));
                $reservationEndDate = date('Y-m-d', strtotime($reservationEnd));

                if ($reservationStartDate >= $rangeFilterStart && $reservationEndDate <= $rangeFilterEnd) {
                    $filteredDataRange[] = $reservation;
                }
            }
            return response()->json($filteredDataRange);
        }

        // Filtro por Ruta (route_id)
        $routeFilter = $request->input('route_id');
        if ($routeFilter) {
            $filteredDataRoute = [];
            foreach ($reservationsArray['reservations'] as $reservation) {
                if ($reservation['route_id'] == $routeFilter) {
                    $filteredDataRoute[] = $reservation;
                }
            }
            return response()->json($filteredDataRoute);
        }

        // Filtro de días deshabilitados
        $filteredDataDisabled = [];
        foreach ($reservationsArray['reservations'] as $reservation) {
            $reservationStart = $reservation['reservation_start'];
            $reservationStartDate = date('d-m-Y', strtotime($reservationStart));

            // Verificar si el día está habilitado o deshabilitado
            $isDisabled = false;
            foreach ($disabledDaysArray['calendar_days_disabled'] as $disabledDay) {
                if ($disabledDay['day'] == $reservationStartDate && !$disabledDay['enabled']) {
                    $isDisabled = true;
                    break;
                }
            }

            // Añadir el día a los datos filtrados solo si no está deshabilitado
            if (!$isDisabled) {
                $filteredDataDisabled[] = $reservation;
            }
        }

        // Verificar si el día está fuera de frecuencia
        $filteredDataFrequency = array_filter($filteredDataDisabled, function ($reservation) use ($routesArray) {
            $reservationStart = $reservation['reservation_start'];
            $reservationStartDate = date('Y-m-d', strtotime($reservationStart));

            // Verificar si el día está fuera de frecuencia
            foreach ($routesArray['routes_data'] as $routeData) {
                if ($routeData['route_id'] == $reservation['route_id']) {
                    $reservationDayOfWeek = date('D', strtotime($reservationStartDate));
                    $reservationDayOfWeek = strtolower($reservationDayOfWeek);

                    return $routeData[$reservationDayOfWeek] != 0;
                }
            }

            return true;
        });

        // Días Reservados
        $filteredDataReserved = [];
        foreach ($filteredDataFrequency as $reservation) {
            $reservationStart = $reservation['reservation_start'];
            $reservationEnd = $reservation['reservation_end'];

            // Ajustar el formato de las fechas
            $reservationStartDate = date('Y-m-d', strtotime($reservationStart));
            $reservationEndDate = date('Y-m-d', strtotime($reservationEnd));

            // Agregar todos los días de la reserva al conjunto de datos filtrados
            $currentDate = $reservationStartDate;
            while ($currentDate <= $reservationEndDate) {
                $filteredDataReserved[] = $currentDate;
                $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
            }
        }

        // Día con servicio
        $filteredDataService = [];
        foreach ($servicesArray['services'] as $service) {
            $serviceTimestamp = $service['timestamp'];
            $serviceDate = date('Y-m-d', strtotime($serviceTimestamp));

            // Agregar el día con servicio al conjunto de datos filtrados
            $filteredDataService[] = $serviceDate;
        }

        // Capacidad de la ruta full
        $filteredDataCapacity = [];
        foreach ($servicesArray['services'] as $service) {
            $capacity = $service['capacity'];

            // Agregar la capacidad de la ruta al conjunto de datos filtrados
            $filteredDataCapacity[] = $capacity;
        }

        return response()->json($filteredDataCapacity);
    }

}
