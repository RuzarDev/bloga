





<?php
include SITE_ROOT . "/app/database/db.php";

$errMsg = [];
$user = null; // Инициализация переменной $user

function userAuth($user){
    $_SESSION['id'] = $user['id'];
    $_SESSION['login'] = $user['username'];
    $_SESSION['admin'] = $user['admin'];
    if($_SESSION['admin']){
        header('location: ' . BASE_URL . "admin/posts/index.php");
    }else{
        header('location: ' . BASE_URL);
    }
}

$users = selectAll('users');

// Код для формы регистрации
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['button-reg'])){
    $admin = 0;
    $login = isset($_POST['login']) ? trim($_POST['login']) : '';
    $email = isset($_POST['mail']) ? trim($_POST['mail']) : '';
    $passF = isset($_POST['pass-first']) ? trim($_POST['pass-first']) : '';
    $passS = isset($_POST['pass-second']) ? trim($_POST['pass-second']) : '';

    if($login === '' || $email === '' || $passF === ''){
        array_push($errMsg, "Не все поля заполнены!");
    } elseif (mb_strlen($login, 'UTF8') < 2){
        array_push($errMsg, "Логин должен быть более 2-х символов");
    } elseif ($passF !== $passS) {
        array_push($errMsg, "Пароли в обеих полях должны соответствовать!");
    } else {
        $existence = selectOne('users', ['email' => $email]);
        if(!empty($existence) && $existence['email'] === $email){
            array_push($errMsg, "Пользователь с такой почтой уже зарегистрирован!");
        } else {
            $pass = password_hash($passF, PASSWORD_DEFAULT);
            $post = [
                'admin' => $admin,
                'username' => $login,
                'email' => $email,
                'password' => $pass
            ];
            $id = insert('users', $post);
            $user = selectOne('users', ['id' => $id] );
            userAuth($user);
        }
    }
} else {
    $login = '';
    $email = '';
}

// Код для формы авторизации
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['button-log'])){
    $email = isset($_POST['mail']) ? trim($_POST['mail']) : '';
    $pass = isset($_POST['password']) ? trim($_POST['password']) : '';

    if($email === '' || $pass === '') {
        array_push($errMsg, "Не все поля заполнены!");
    } else {
        $existence = selectOne('users', ['email' => $email]);
        if(!empty($existence) && password_verify($pass, $existence['password'])){
            userAuth($existence);
        } else {
            array_push($errMsg, "Почта либо пароль введены неверно!");
        }
    }
} else {
    $email = '';
}

// Остальной код...

// Теперь вы можете безопасно использовать переменную $user во всем скрипте, избегая предупреждений о неопределенной переменной.

// Код удаления пользователя в админке
if($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete_id'])){
    $id = $_GET['delete_id'];
    delete('users', $id);
    header('location: ' . BASE_URL . 'admin/users/index.php');
}

// РЕДАКТИРОВАНИЕ ПОЛЬЗОВАТЕЛЯ ЧЕРЕЗ АДМИНКУ
if($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['edit_id'])){
    $user = selectOne('users', ['id' => $_GET['edit_id']]);

    $id = isset($user['id']) ? $user['id'] : '';
    $admin = isset($user['admin']) ? $user['admin'] : '';
    $username = isset($user['username']) ? $user['username'] : '';
    $email = isset($user['email']) ? $user['email'] : '';
}

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update-user'])){

    $id = isset($_POST['id']) ? $_POST['id'] : '';
    $mail = isset($_POST['mail']) ? trim($_POST['mail']) : '';
    $login = isset($_POST['login']) ? trim($_POST['login']) : '';
    $passF = isset($_POST['pass-first']) ? trim($_POST['pass-first']) : '';
    $passS = isset($_POST['pass-second']) ? trim($_POST['pass-second']) : '';
    $admin = isset($_POST['admin']) ? 1 : 0;

    if($login === ''){
        array_push($errMsg, "Не все поля заполнены!");
    } elseif (mb_strlen($login, 'UTF8') < 2){
        array_push($errMsg, "Логин должен быть более 2-х символов");
    } elseif ($passF !== $passS) {
        array_push($errMsg, "Пароли в обеих полях должны соответствовать!");
    } else {
        $pass = password_hash($passF, PASSWORD_DEFAULT);
        if (isset($_POST['admin'])) $admin = 1;
        $user = [
            'admin' => $admin,
            'username' => $login,
            'password' => $pass
        ];

        $user = update('users', $id, $user);
        header('location: ' . BASE_URL . 'admin/users/index.php');
    }
} else {
    if($user !== null && $user !== false) {
        $id =  isset($user['id']) ? $user['id'] : '';
        $admin =  isset($user['admin']) ? $user['admin'] : '';
        $username = isset($user['username']) ? $user['username'] : '';
        $email = isset($user['email']) ? $user['email'] : '';
    }
}

// Код удаления пользователя в админке
if($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete_id'])){
    $id = $_GET['delete_id'];
    delete('users', $id);
    header('location: ' . BASE_URL . 'admin/users/index.php');
}
