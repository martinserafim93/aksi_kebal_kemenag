<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="AKSI KEBAL - Sistem Informasi Absensi Kegiatan Pegawai Kementerian Agama">
    <title><?= e($title ?? APP_NAME) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0f9b58 0%, #1abc9c 50%, #16a085 100%);
            color: #333;
        }

        .container {
            text-align: center;
            padding: 2rem;
            max-width: 600px;
        }

        .logo {
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .logo-text {
            font-size: 1.8rem;
            font-weight: 700;
            color: #fff;
            letter-spacing: 1px;
        }

        h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .subtitle {
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .btn-group {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.8rem 2rem;
            border-radius: 12px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.95rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
        }

        .btn-primary {
            background: #fff;
            color: #0f9b58;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
        }

        .btn-outline {
            background: transparent;
            color: #fff;
            border: 2px solid rgba(255, 255, 255, 0.5);
        }

        .btn-outline:hover {
            background: rgba(255, 255, 255, 0.15);
            border-color: #fff;
            transform: translateY(-2px);
        }

        .footer {
            margin-top: 3rem;
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.8rem;
        }

        @media (max-width: 480px) {
            h1 {
                font-size: 1.8rem;
            }

            .btn-group {
                flex-direction: column;
                align-items: center;
            }

            .btn {
                width: 100%;
                max-width: 280px;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <span class="logo-text">AK</span>
        </div>
        <h1><?= e(APP_NAME) ?></h1>
        <p class="subtitle"><?= e(APP_FULL_NAME) ?><br>Sistem Informasi Absensi Kegiatan Pegawai</p>
        
        <div class="btn-group">
            <a href="<?= url('admin/login') ?>" class="btn btn-primary">
                🔐 Login Admin
            </a>
        </div>

        <div class="footer">
            <p>&copy; <?= date('Y') ?> <?= e(APP_NAME) ?> v<?= APP_VERSION ?></p>
        </div>
    </div>
</body>
</html>
