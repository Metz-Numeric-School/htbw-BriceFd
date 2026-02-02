<?php
namespace App\Controller;

use App\Repository\UserRepository;
use Mns\Buggy\Core\AbstractController;

class SecurityController extends AbstractController
{

    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

      public function login()
    {
        if(!empty($_SESSION['user']))
        {
            $_SESSION['admin'] ? header('Location: /admin/dashboard') : header('Location: /user/dashboard'); die;
        }

        $error = null;

        if(!empty($_POST)) {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            $user = $this->userRepository->findByEmail($username);

            if(!$user) {
                $error = 'Invalid username or password';
            } else {
                // debug -> écrit dans var/logs/debug.log (créer le dossier si nécessaire)
                error_log('DEBUG findByEmail result: '.print_r($user, true)."\n", 3, __DIR__ . '/../../var/logs/debug.log');
                error_log('DEBUG input password: '.substr($password,0,50)."\n", 3, __DIR__ . '/../../var/logs/debug.log');

                if (password_verify($password, $user->getPassword())) {

                    $_SESSION['user'] = [
                        'id' => $user->getId(),
                        'username' => $user->getFirstname(),
                    ];

                    if($user->getIsadmin()) {
                        $_SESSION['admin'] = $user->getIsadmin();
                        header('Location: /admin/dashboard');
                        exit;
                    } else {
                        header('Location: /dashboard');
                        exit;
                    }
                } else {
                    $error = 'Invalid username or password';
                }
            }
        }

        return $this->render('security/login.html.php', [
            'title' => 'Login',
            'error' => $error ?? null,
        ]);
    }
    public function logout()
    {
        unset($_SESSION['user']);
        unset($_SESSION['admin']);
        session_destroy();
        header('Location: /login');
        exit;
    }
}