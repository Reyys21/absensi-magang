<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
    html,
    body {
        margin: 0;
        padding: 0;
        height: 100%;
        font-family: 'Inter', sans-serif;
    }

    .bg-left {
        background: url('{{ asset('assets/images/VECTOR.png') }}') no-repeat left center;
        background-size: cover;
        background-position: left -80px top;
    }

    .pln-brand img {
        width: 50px;
        height: 50px;
    }

    .pln-brand h6 {
        font-size: 16px;
    }

    .pln-brand p {
        font-size: 12px;
    }

    .custom-travel-img {
        max-width: 700px;
        width: 100%;
        height: auto;
        object-fit: contain;
        transform: translateX(0);
    }

    .form-container {
        max-width: 400px;
        width: 100%;
    }

    .login-form input {
        border-radius: 8px;
    }

    .login-form button {
        border-radius: 8px;
        background-color: black;
        color: white;
        font-weight: bold;
    }

    .login-form button:hover {
        background-color: #333;
    }

    /* MOBILE (min 480px) */
    @media only screen and (min-width: 480px) {
        .custom-travel-img {
            max-width: 250px;
        }

        .pln-brand img {
            width: 45px;
            height: 45px;
        }
    }

    /* TABLET (min 768px) */
    @media only screen and (min-width: 768px) {
        .bg-left {
            background-position: left -120px top -10px;
        }

        .custom-travel-img {
            max-width: 300px;
        }
    }

    /* DESKTOP (min 992px) */
    @media only screen and (min-width: 992px) {
        .bg-left {
            background-position: left -52px top -20px;
        }

        .custom-travel-img {
            max-width: 420px;
        }
    }

    /* HUGE screen (min 1280px) */
    @media only screen and (min-width: 1280px) {
        .bg-left {
            background-position: left -72px top -20px;
        }

        .custom-travel-img {
            max-width: 600px;
        }
    }

    /* MOBILE fallback bg */
    @media only screen and (max-width: 768px) {
        .bg-left {
            background-color: #0B849F;
            background-image: none;
            text-align: center;
            padding: 2rem 1rem;
        }

        .pln-brand {
            justify-content: center;
            align-items: center;
            display: flex;
            flex-direction: column;
        }



        .custom-travel-img {
            max-width: 200px;
            margin-top: 1rem;
        }

        .form-container h4 {
            font-size: 22px;
        }
    }
    </style>
</head>

<body>
    <div class="d-flex flex-column flex-md-row h-100">

        <!-- Left Side -->
        <div class="bg-left d-flex flex-column justify-content-center align-items-start w-100 w-md-50 p-4">
            <div class="pln-brand mb-3 text-white">
                <img src="{{ asset('assets/images/Logo_PLN.png') }}" alt="Logo PLN" class="mb-2">
                <h6 class="text-pln" class="fw-bold m-0">PLN</h6>
                <p class="m-0">UID KALSELTENG</p>
            </div>

            <div class="d-flex justify-content-center align-items-center w-100 mt-3">
                <img src="{{ asset('assets/images/undraw_traveling_c18z (1).svg') }}" alt="Travel"
                    class="custom-travel-img">
            </div>
        </div>

        <!-- Right Side -->
        <div class="d-flex flex-column justify-content-center align-items-center w-100 w-md-50 px-4 py-5">
            <div class="form-container">
                <h4 class="text-center fw-bold mb-2">WELCOME</h4>
                <p class="text-center text-muted mb-4">Welcome to the Website for Internship Absences. Please Login
                    First</p>

                @if ($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="/login" class="login-form">
                    @csrf
                    <div class="mb-3">
                        <input type="email" name="email" class="form-control" placeholder="Enter Your Gmail" required
                            autofocus>
                    </div>
                    <div class="mb-4">
                        <input type="password" name="password" class="form-control" placeholder="Enter Your Password"
                            required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-dark">LOGIN</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>