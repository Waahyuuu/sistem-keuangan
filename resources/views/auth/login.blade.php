<!DOCTYPE html>
<html>

<head>
    <title>Login - Keuangan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container d-flex justify-content-center align-items-center vh-100">

        <div class="card p-4 shadow" style="width:400px;">
            <h4 class="mb-3 text-center">Login Keuangan</h4>

            <form method="POST" action="/login">
                @csrf

                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control">
                </div>

                <div class="mb-3">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control">
                </div>

                <button class="btn btn-primary w-100">Login</button>
            </form>

            @if ($errors->any())
            <div class="alert alert-danger mt-3">
                {{ $errors->first() }}
            </div>
            @endif

        </div>

    </div>

</body>

</html>