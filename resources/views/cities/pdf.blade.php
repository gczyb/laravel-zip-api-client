<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Városok listája</title>
    <style>
        @page {
            margin: 100px 50px 80px 50px;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #333;
        }
        header {
            position: fixed;
            top: -80px;
            left: 0;
            right: 0;
            height: 60px;
            background-color: #667eea;
            color: white;
            text-align: center;
            padding: 15px 0;
        }
        header .logo {
            font-size: 24px;
            font-weight: bold;
            margin: 0;
        }
        header .subtitle {
            font-size: 12px;
            margin: 5px 0 0 0;
        }
        footer {
            position: fixed;
            bottom: -60px;
            left: 0;
            right: 0;
            height: 40px;
            background-color: #f8f9fa;
            border-top: 2px solid #667eea;
            text-align: center;
            line-height: 40px;
            font-size: 10px;
            color: #666;
        }
        .page-number:after {
            content: counter(page);
        }
        h1 {
            color: #667eea;
            border-bottom: 3px solid #667eea;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #667eea;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }
        td {
            padding: 8px 10px;
            border-bottom: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .metadata {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
        }
        .metadata p {
            margin: 5px 0;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            background-color: #6c757d;
            color: white;
            border-radius: 3px;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <header>
        <p class="logo">Irányítószám Kereső</p>
        <p class="subtitle">Városok listája</p>
    </header>

    <footer>
        <span>Generálva: {{ $date }}</span>
        <span style="margin: 0 20px;">|</span>
        <span>Oldal: <span class="page-number"></span></span>
        <span style="margin: 0 20px;">|</span>
        <span>&copy; {{ date('Y') }}</span>
    </footer>

    <div class="content">
        <h1>{{ $title }}</h1>

        <div class="metadata">
            <p><strong>Generálás időpontja:</strong> {{ $date }}</p>
            <p><strong>Találatok száma:</strong> {{ count($cities) }} db</p>
        </div>

        @if(count($cities) > 0)
            <table>
                <thead>
                    <tr>
                        <th style="width: 10%;">Sorszám</th>
                        <th style="width: 40%;">Város neve</th>
                        <th style="width: 30%;">Megye</th>
                        <th style="width: 20%;">Irányítószám</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cities as $index => $city)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><strong>{{ $city['name'] }}</strong></td>
                            <td>{{ $city['county']['name'] ?? 'N/A' }}</td>
                            <td>
                                @if(isset($city['postal_codes']) && count($city['postal_codes']) > 0)
                                    @foreach($city['postal_codes'] as $postalCode)
                                        <span class="badge">{{ $postalCode['code'] }}</span>
                                    @endforeach
                                @else
                                    <span style="color: #999;">N/A</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div style="padding: 40px; text-align: center; background-color: #f8f9fa; border-radius: 5px;">
                <p style="font-size: 14px; color: #666;">Nincs megjeleníthető adat.</p>
            </div>
        @endif
    </div>
</body>
</html>