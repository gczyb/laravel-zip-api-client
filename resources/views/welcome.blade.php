<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 100px 0;
            text-align: center;
        }
        .feature-box {
            padding: 30px;
            margin: 20px 0;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-md navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/">{{ config('app.name') }}</a>
            <div class="ms-auto">
                @auth
                    <a href="{{ url('/home') }}" class="btn btn-outline-light">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-light me-2">Bejelentkezés</a>
                    <a href="{{ route('register') }}" class="btn btn-light">Regisztráció</a>
                @endauth
            </div>
        </div>
    </nav>

    <div class="hero-section">
        <div class="container">
            <h1 class="display-3 fw-bold mb-4">Magyar Irányítószám Kereső</h1>
            <p class="lead mb-5">Könnyedén kereshetsz és kezelhetsz magyar irányítószámokat, városokat és megyéket</p>
            <div>
                <a href="{{ route('counties.index') }}" class="btn btn-light btn-lg me-3">Megyék böngészése</a>
                <a href="{{ route('cities.index') }}" class="btn btn-outline-light btn-lg">Városok keresése</a>
            </div>
        </div>
    </div>

    <div class="container my-5">
        <div class="row">
            <div class="col-md-4">
                <div class="feature-box text-center">
                    <h3>🗺️ Megyék</h3>
                    <p>Böngészd át Magyarország összes megyéjét és azok városait</p>
                    <a href="{{ route('counties.index') }}" class="btn btn-primary">Megyék megtekintése</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-box text-center">
                    <h3>🏙️ Városok</h3>
                    <p>Keress városokat ABC sorrendben, megye szerint csoportosítva</p>
                    <a href="{{ route('cities.index') }}" class="btn btn-primary">Városok keresése</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-box text-center">
                    <h3>📄 Export</h3>
                    <p>Exportáld az adatokat CSV vagy PDF formátumban</p>
                    @auth
                        <a href="{{ route('cities.index') }}" class="btn btn-primary">Export funkciók</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-secondary">Jelentkezz be</a>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>