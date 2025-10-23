<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Форма логина с CAPTCHA</title>
    <style>
        .captcha-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            margin: 20px;
        }
        .captcha-item {
            width: 100px;
            height: 100px;
            margin: 10px;
            border: 1px solid #ccc;
            background-color: #f9f9f9;
        }
        .captcha-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        #captcha-form {
            text-align: center;
            margin-top: 20px;
        }
        #result {
            margin-top: 20px;
            font-weight: bold;
        }
        .form-container {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
        button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }
    </style>
</head>
<body>

<div class="form-container">
    <form method="POST" action="/login">
        @csrf
        <div class="form-group">
            <label>Логин:</label>
            <input type="text" name="login" required>
        </div>
        <div class="form-group">
            <label>Пароль:</label>
            <input type="password" name="password" required>
        </div>

        <div>
            <div id="captcha-container" class="captcha-container">
            </div>
            <button type="button" onclick="checkOrder()">Проверить CAPTCHA</button>
            <div id="result"></div>
        </div>
        <button type="submit" id="submit-button" disabled>Войти</button>
    </form>

    @if (session('error'))
        <div style="color: red;">
            {{ session('error') }}
        </div>
    @endif
</div>
<script src="{{ asset('js/Sortable.min.js') }}"></script>
<script>

    const images = ['../../public/img/1.png', '../../public/img/2.png', '../../public/img/3.png', '../../public/img/4.png'];
    const correctOrder = ['../../public/img/1.png', '../../public/img/2.png', '../../public/img/3.png', '../../public/img/4.png'];

    function shuffle(array) {
        return array.sort(() => Math.random() - 0.5);
    }

    function initCaptcha() {
        const container = document.getElementById('captcha-container');
        container.innerHTML = '';
        const shuffled = shuffle([...images]);
        shuffled.forEach(src => {
            const div = document.createElement('div');
            div.className = 'captcha-item';
            div.innerHTML = `<img src="${src}" alt="CAPTCHA image">`;
            container.appendChild(div);
        });
        new Sortable(container, {
            animation: 150,
            ghostClass: 'sortable-ghost'
        });
    }

    function checkOrder() {
        const items = Array.from(document.querySelectorAll('#captcha-container .captcha-item img'));
        const currentOrder = items.map(img => img.src);
        const result = document.getElementById('result');
        const submitButton = document.getElementById('submit-button');
        if (JSON.stringify(currentOrder) === JSON.stringify(correctOrder.map(src => new URL(src, window.location.href).href))) {
            result.textContent = 'CAPTCHA пройдена!';
            result.style.color = 'green';
            submitButton.disabled = false; 
        } else {
            result.textContent = 'Неправильный порядок. Попробуйте снова.';
            result.style.color = 'red';
            initCaptcha();
            submitButton.disabled = true; 
        }
    }

    initCaptcha();
</script>