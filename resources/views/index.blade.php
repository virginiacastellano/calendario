<!-- resources/views/index.blade.php -->

<!DOCTYPE html>
<html>
<head>
    <title>Filtered Data</title>
</head>
<body>
    <h1>Filtered Data</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>User Plan ID</th>
                <th>Route ID</th>
                <th>Track ID</th>
                <th>Reservation Start</th>
                <th>Reservation End</th>
                <th>Route Stop Origin ID</th>
                <th>Route Stop Destination ID</th>
                <th>Created At</th>
                <th>Updated At</th>
                <th>Deleted At</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($filteredData as $reservation)
                <tr>
                    <td>{{ $reservation['id'] }}</td>
                    <td>{{ $reservation['user_plan_id'] }}</td>
                    <td>{{ $reservation['route_id'] }}</td>
                    <td>{{ $reservation['track_id'] }}</td>
                    <td>{{ $reservation['reservation_start'] }}</td>
                    <td>{{ $reservation['reservation_end'] }}</td>
                    <td>{{ $reservation['route_stop_origin_id'] }}</td>
                    <td>{{ $reservation['route_stop_destination_id'] }}</td>
                    <td>{{ $reservation['created_at'] }}</td>
                    <td>{{ $reservation['updated_at'] }}</td>
                    <td>{{ $reservation['deleted_at'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
