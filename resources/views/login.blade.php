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

</body>
</html>