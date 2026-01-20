<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Пользователи</title>

    <style>
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
            background: #f5f6f8;
            margin: 0;
            padding: 40px;
            color: #222;
        }

        .container {
            max-width: 720px;
            margin: 0 auto;
        }

        h1 {
            margin-bottom: 24px;
            font-size: 26px;
        }

        .card {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            margin-bottom: 32px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        label {
            display: block;
            font-size: 14px;
            margin-bottom: 6px;
            color: #555;
        }

        input[type="text"],
        input[type="file"] {
            width: 100%;
            padding: 10px 12px;
            border-radius: 8px;
            border: 1px solid #ddd;
            font-size: 14px;
        }

        input[type="file"] {
            padding: 8px;
        }

        button {
            background: #4f46e5;
            color: #fff;
            border: none;
            padding: 10px 16px;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
        }

        button:hover {
            background: #4338ca;
        }

        ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        li.user {
            background: #fff;
            border-radius: 12px;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.04);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            object-fit: cover;
            background: #eee;
        }

        .nickname {
            font-weight: 500;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Пользователи</h1>

        <div class="card">
            <form id="registerForm" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label for="nickname">Никнейм</label>
                    <input type="text" name="nickname" id="nickname" placeholder="Введите никнейм" required>
                </div>

                <div class="form-group">
                    <label for="avatar">Аватар</label>
                    <input type="file" name="avatar" id="avatar" accept="image/*" required>
                </div>

                <button type="submit">Зарегистрироваться</button>
            </form>
        </div>

        <ul>
            @foreach ($users as $user)
            <li class="user">
                <div class="user-info">
                    <img class="avatar" src="{{ $user->avatar_url }}" alt="{{ $user->nickname }}">
                    <span class="nickname">{{ $user->nickname }}</span>
                </div>
            </li>
            @endforeach
        </ul>
    </div>

    <script>
        document.getElementById('registerForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const form = e.target;
            const data = new FormData(form);

            try {
                const token = document.querySelector('input[name="_token"]').value;
                const response = await fetch('/api/register', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                    body: data
                });

                const result = await response.json();

                if (response.ok) {
                    alert('Пользователь зарегистрирован успешно!');
                    form.reset();
                } else {
                    let msg = 'Ошибка валидации:\n';

                    if (result.data) {
                        for (const field in result.data) {
                            const messages = result.data[field];
                            messages.forEach(m => {
                                msg += `• ${m}\n`;
                            });
                        }
                    } else if (result.status && result.status.message) {
                        msg += result.status.message;
                    } else {
                        msg += 'Неизвестная ошибка';
                    }

                    alert(msg);
                }
            } catch (err) {
                alert('Произошла ошибка при отправке запроса');
                console.error(err);
            }
        });
    </script>
</body>

</html>