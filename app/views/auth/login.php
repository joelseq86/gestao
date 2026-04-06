<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — <?= h(APP_NAME) ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<div class="login-page">
    <div class="login-card">
        <div class="logo">
            <h1>💼 <?= h(APP_NAME) ?></h1>
            <p>Gestão financeira empresarial</p>
        </div>

        <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= h($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="/login">
            <?= csrfField() ?>
            <div class="form-group mb-2">
                <label for="username">Utilizador</label>
                <input type="text" id="username" name="username"
                       value="<?= h($_POST['username'] ?? '') ?>"
                       placeholder="admin" required autofocus>
            </div>
            <div class="form-group mb-3">
                <label for="password">Palavra-passe</label>
                <input type="password" id="password" name="password"
                       placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">
                Entrar
            </button>
        </form>
    </div>
</div>
<script src="/assets/js/app.js"></script>
</body>
</html>
