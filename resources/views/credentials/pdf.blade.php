<!DOCTYPE html>
<html>
<head>
    <title>Credentials PDF</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        h2 { text-align: center; }
    </style>
</head>
<body>
    <h2>Credentials Report</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Username</th>
                <th>Email</th>
                <th>Password</th>
            </tr>
        </thead>
        <tbody>
            @foreach($credentials as $cred)
            <tr>
                <td>{{ $cred->id }}</td>
                <td>{{ $cred->name }}</td>
                <td>{{ $cred->username ?: '-' }}</td>
                <td>{{ $cred->email ?: '-' }}</td>
                <td>{{ $cred->password }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
