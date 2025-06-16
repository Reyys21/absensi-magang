<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Mahasiswa</title>
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

        .login-form input, .login-form select {
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

        .top-signup {
            position: absolute;
            top: 20px;
            right: 30px;
            font-size: 14px;
        }

        .top-signup a {
            margin-left: 8px;
            padding: 5px 12px;
            border: 1px solid #000;
            border-radius: 20px;
            text-decoration: none;
            font-weight: 600;
            color: black;
            font-size: 14px;
        }

        .top-signup a:hover {
            background-color: #000;
            color: white;
        }

        @media only screen and (min-width: 480px) {
            .custom-travel-img {
                max-width: 250px;
            }

            .pln-brand img {
                width: 45px;
                height: 45px;
            }
        }

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

            .top-signup {
                top: 15px;
                right: 15px;
                font-size: 13px;
                color: white;
            }

            .top-signup span {
                color: white;
            }

            .top-signup a {
                padding: 4px 10px;
                font-size: 13px;
                color: white;
                border-color: white;
            }

            .top-signup a:hover {
                background-color: white;
                color: #0B849F;
            }
        }


        @media only screen and (min-width: 992px) {
            .bg-left {
                background-position: left -52px top -20px;
            }

            .custom-travel-img {
                max-width: 420px;
            }
        }

        @media only screen and (min-width: 1280px) {
            .bg-left {
                background-position: left -72px top -20px;
            }

            .custom-travel-img {
                max-width: 600px;
            }
        }
    </style>
</head>

<body>
    <div class="top-signup">
        <span>Sudah punya akun?</span>
        <a href="{{ route('login') }}">MASUK</a>
    </div>

    <div class="d-flex flex-column flex-md-row h-100">
        <div class="bg-left d-flex flex-column justify-content-center align-items-start w-100 w-md-50 p-4">
            <div class="pln-brand mb-3 text-white">
                <img src="{{ asset('assets/images/Logo_PLN.png') }}" alt="Logo PLN" class="mb-2">
                <h6 class="fw-bold m-0">PLN</h6>
                <p class="m-0">UID KALSELTENG</p>
            </div>
            <div class="d-flex justify-content-center align-items-center w-100 mt-3">
                <img src="{{ asset('assets/images/undraw_traveling_c18z (1).svg') }}" alt="Perjalanan"
                    class="custom-travel-img">
            </div>
        </div>

        <div class="d-flex flex-column justify-content-center align-items-center w-100 w-md-50 px-4 py-5">
            <div class="form-container">
                <h4 class="text-center fw-bold mb-2">DAFTAR</h4>
                <p class="text-center text-muted mb-4">Silakan buat akun Anda untuk melanjutkan</p>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" class="login-form">
                    @csrf

                    <div class="mb-3">
                        <select name="role" class="form-control" required>
                            <option value="" disabled {{ old('role') ? '' : 'selected' }}>Daftar sebagai</option>
                            <option value="mahasiswa" {{ old('role') == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                            <option value="siswa" {{ old('role') == 'siswa' ? 'selected' : '' }}>Siswa</option>
                        </select>
                    </div>

                    <div class="mb-3 d-flex gap-2">
                        <input type="text" name="name" class="form-control" placeholder="Nama" value="{{ old('name') }}" required>
                        <input type="email" name="email" class="form-control" placeholder="Email" value="{{ old('email') }}" required>
                    </div>

                    <div class="mb-3">
                        <input type="text" name="asal_kampus" class="form-control" placeholder="Asal Kampus" value="{{ old('asal_kampus') }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <select name="bidang_id" id="bidang_id" class="form-control" required>
                            <option value="" disabled selected>Pilih Bidang/Departemen</option>
                            @foreach ($bidangs as $bidang)
                                <option value="{{ $bidang->id }}" {{ old('bidang_id') == $bidang->id ? 'selected' : '' }}>
                                    {{ $bidang->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <input type="text" name="phone" class="form-control" placeholder="Nomor Telepon" value="{{ old('phone') }}">
                    </div>

                    <div class="mb-3">
                        <input type="text" name="nim" class="form-control" placeholder="NIM (Opsional)" value="{{ old('nim') }}">
                    </div>

                    <div class="mb-3">
                        <input type="password" name="password" class="form-control" placeholder="Kata Sandi" required>
                    </div>

                    <div class="mb-4">
                        <input type="password" name="password_confirmation" class="form-control"
                            placeholder="Konfirmasi Kata Sandi" required>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-dark">DAFTAR</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>