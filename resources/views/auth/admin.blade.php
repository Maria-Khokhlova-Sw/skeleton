<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Администратор</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        button {
            padding: 8px 15px;
            background: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background: #0056b3;
        }
        .form-container {
            display: none;
            margin: 20px 0;
            padding: 15px;
            border: 1px solid #ccc;
            background: #f9f9f9;
        }
        .form-container.active {
            display: block;
        }
        .form-group {
            margin-bottom: 10px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 5px;
        }
        .error {
            color: red;
            margin-bottom: 10px;
        }
        .success {
            color: green;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <h1>Вы администратор!</h1>

    @if (session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="error">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="error">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <button onclick="showAddUserForm()">Добавить пользователя</button>
    
    <div id="add-user-form" class="form-container">
        <h2>Добавить пользователя</h2>
        <form method="POST" action="/users/store">
            @csrf
            <div class="form-group">
                <label>Логин:</label>
                <input type="text" name="login" value="{{ old('login') }}" required>
                @error('login')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label>Пароль:</label>
                <input type="password" name="password" required>
                @error('password')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label>Роль:</label>
                <select name="id_role" required>
                    <option value="1" {{ old('id_role') == 1 ? 'selected' : '' }}>Администратор</option>
                    <option value="2" {{ old('id_role') == 2 ? 'selected' : '' }}>Пользователь</option>
                </select>
                @error('id_role')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>
            <button type="submit">Сохранить</button>
            <button type="button" onclick="hideForms()">Отмена</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Логин</th>
                <th>Роль</th>
                <th>Заблокирован</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody id="users-table">
            @if (isset($users) && $users->count() > 0)
                @foreach ($users as $user)
                <tr>
                    <td>{{ $user->login }}</td>
                    <td>{{ $user->id_role == 2 ? 'Администратор' : 'Пользователь' }}</td>
                    <td>{{ $user->is_blocked ? 'Да' : 'Нет' }}</td>
                    <td>
                        <button onclick="showEditUserForm({{ $user->id }}, '{{ $user->login }}', {{ $user->id_role }}, {{ $user->is_blocked }}, {{ $user->number_attempt }})">Редактировать</button>
                    </td>
                </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="4">Нет пользователей</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div id="edit-user-form" class="form-container">
        <h2>Редактировать пользователя</h2>
        <form method="POST" action="/users/update">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" id="edit-user-id">
            <div class="form-group">
                <label>Логин:</label>
                <input type="text" name="login" id="edit-login" required>
                @error('login')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label>Пароль (оставьте пустым, если не меняете):</label>
                <input type="password" name="password" id="edit-password">
                @error('password')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label>Роль:</label>
                <select name="id_role" id="edit-id_role" required>
                    <option value="1">Администратор</option>
                    <option value="2">Пользователь</option>
                </select>
                @error('id_role')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label>Заблокирован:</label>
                <select name="is_blocked" id="edit-is_blocked" required>
                    <option value="0">Нет</option>
                    <option value="1">Да</option>
                </select>
                @error('is_blocked')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label>Количество попыток:</label>
                <input type="number" name="number_attempt" id="edit-number_attempt" min="0" required>
                @error('number_attempt')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>
            <button type="submit">Сохранить</button>
            <button type="button" onclick="hideForms()">Отмена</button>
        </form>
    </div>

    <form method="POST" action="/logout">
        @csrf
        <button type="submit">Выйти</button>
    </form>

    <script>
        function showAddUserForm() {
            document.getElementById('add-user-form').classList.add('active');
            document.getElementById('edit-user-form').classList.remove('active');
        }

        function showEditUserForm(id, login, id_role, is_blocked, number_attempt) {
            document.getElementById('edit-user-id').value = id;
            document.getElementById('edit-login').value = login;
            document.getElementById('edit-id_role').value = id_role;
            document.getElementById('edit-is_blocked').value = is_blocked;
            document.getElementById('edit-number_attempt').value = number_attempt;
            document.getElementById('edit-user-form').classList.add('active');
            document.getElementById('add-user-form').classList.remove('active');
        }

        function hideForms() {
            document.getElementById('add-user-form').classList.remove('active');
            document.getElementById('edit-user-form').classList.remove('active');
        }
    </script>
</body>
</html>