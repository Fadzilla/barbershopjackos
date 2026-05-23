<<<<<<< HEAD
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>BARBERSHOP</title>
  <link rel="shortcut icon" type="image/png" href="{{ asset('images/logos/favicon.png') }}" />
  <link rel="stylesheet" href="{{ asset('css/styles.min.css') }}" />
</head>

<body>
  <div class="page-wrapper" id="main-wrapper">
    <div class="position-relative overflow-hidden min-vh-100 d-flex align-items-center justify-content-center">
      <div class="w-100">
        <div class="row justify-content-center">
          <div class="col-md-8 col-lg-6 col-xxl-3">
            <div class="card">
              <div class="card-body">

                <h3 class="text-center mb-3">Login</h3>

                <!-- ERROR -->
                @if ($errors->any())
                  <div class="alert alert-danger">
                    <ul>
                      @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                      @endforeach
                    </ul>
                  </div>
                @endif

                <!-- FORM LOGIN -->
                <form method="POST" action="/login">
                  @csrf

                  <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" required>
                  </div>

                  <div class="mb-3">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required>
                  </div>

                  <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
=======
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Barbershop POS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 flex items-center justify-center h-screen">

    <div class="bg-gray-800 text-white p-8 rounded-2xl shadow-lg w-full max-w-md">
        
        <h2 class="text-2xl font-bold text-center mb-6">💈 Barbershop POS</h2>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email -->
            <div class="mb-4">
                <label class="block mb-1">Email</label>
                <input type="email" name="email" 
                    class="w-full p-2 rounded bg-gray-700 border border-gray-600 focus:outline-none">
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label class="block mb-1">Password</label>
                <input type="password" name="password" 
                    class="w-full p-2 rounded bg-gray-700 border border-gray-600 focus:outline-none">
            </div>

            <!-- Remember -->
            <div class="mb-4 flex items-center">
                <input type="checkbox" name="remember" class="mr-2">
                <span>Remember me</span>
            </div>

            <!-- Button -->
            <button type="submit" 
                class="w-full bg-yellow-500 hover:bg-yellow-600 text-black font-bold py-2 rounded">
                Login
            </button>

            <!-- Error -->
            @if ($errors->any())
                <div class="mt-4 text-red-400 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif
        </form>
    </div>
>>>>>>> 02da6b2840462e089e350298efcab23613ab7d51

</body>
</html>