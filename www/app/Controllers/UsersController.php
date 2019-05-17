<?php

declare (strict_types = 1);

namespace Songfolio\controllers;

use Songfolio\Core\Helper;
use Songfolio\core\View;
use Songfolio\core\Routing;
use Songfolio\core\Validator;
use Songfolio\models\Users;
use Songfolio\Models\Roles;

class UsersController
{

    private $user;
    private $role;

    public function __construct(Users $user, Roles $role)
    {
        $this->user = $user;
        $this->role = $role;
    }


    public function defaultAction(): void
    {
        echo "User default";
    }

    public function dashboardAction(): void
    {
        $v = new View("user_dashboard", "front");
        $v->assign('user', $this->user);
    }

    public function registerAction(): void
    {
        if ($this->user->is('connected')) {
                header('Location: ' . Routing::getSlug('users', 'dashboard'));
            }

        $configForm = $this->user->getFormRegister();

        if (!empty($_POST)) {

            $method = $configForm["config"]["method"];
            $data = $GLOBALS["_" . $method];

            if ($_SERVER["REQUEST_METHOD"] == $method && !empty($data)) {
                $validator = new Validator($configForm, $data);
                $configForm["errors"] = $validator->getErrors();

                if (empty($configForm["errors"])) {
                    $this->user->__set('username', $data["username"]);
                    $this->user->__set('email', $data["email"]);
                    $this->user->__set('password', $data["password"]);
                    $this->user->save();
                }
            }
        }

        $v = new View("user_register", "front");
        $v->assign("configFormRegister", $configForm);
    }

    public function loginAction(): void
    {
        if ($this->user->is('connected')) {
                header('Location: ' . Routing::getSlug('users', 'dashboard'));
            }

        $user = new Users();
        $configForm = $user->getFormLogin();

        if (!empty($_POST)) {

            $method = $configForm["config"]["method"];
            $data = $GLOBALS["_" . $method];

            if ($_SERVER["REQUEST_METHOD"] == $method && !empty($data)) {
                $validator = new Validator($configForm, $data);
                $configForm["errors"] = $validator->getErrors();

                if (empty($configForm["errors"])) {
                    if ($user->getOneBy(['username' => $data['username']], true) && password_verify($data['password'], $user->__get('password'))) {

                        $token = md5(substr(uniqid().time(), 4, 10));
                        setcookie('token', $token, time() + (86400 * 7), "/");

                        $user->__set('login_token', $token);
                        $user->save();
                        $_SESSION['user'] = $user->__get('id');

                        if (isset($_GET['redirect'])) {
                                $redirect = htmlspecialchars(urldecode($_GET['redirect']));
                                header('Location: ' . $redirect);
                                exit;
                            }

                        header('Location: ' . Routing::getSlug("users", "dashboard"));
                    } else {
                        $configForm["errors"][] = "Incorrect";
                    }
                }
            }
        }

        $v = new View("user_login", "front");
        $v->assign("configFormLogin", $configForm);
    }

    public function logoutAction(): void
    {
        unset($_SESSION['user']);
        setcookie('token', '', -1, '/');

        header('Location: ' . BASE_URL);
    }

    public function forgetPasswordAction(): void
    {
        new View("user_forgetPassword", "front");
    }

    public function createUsersAction()
    {
        $configForm = $this->user->getFormUsers()['create'];
        $roles = $this->role->getAllData();
        $alert = self::push($configForm, 'create');
        $configForm['data']['role']['options'] = Roles::prepareRoleToSelect($roles);
        self::renderUsersView($alert, $configForm);
    }

    private function renderUsersView($alert, array $configForm)
    {
        $view = new View('admin/users/create', 'back');
        if (!empty($alert)) $view->assign('alert', $alert);
        $view->assign('configFormUsers', $configForm);
    }

    private function push($configForm, $action)
    {
        $method = strtoupper($configForm["config"]["method"]);
        $data = $GLOBALS["_" . $method];
        if (!empty($data)) {
            if ($_SERVER["REQUEST_METHOD"] !== $method || empty($data)) {
                return false;
            }
            $validator = new Validator($configForm, $data);
            $errors = $validator->getErrors();

            if (empty($errors) && (!$this->user->getOneBy(['username' => $data['username']]) || isset($_REQUEST['id']))) {
                isset($_REQUEST['id']) ? $this->user->__set('id', $_REQUEST['id']) : null;

                $this->user->__set('username', $data['username']);
                $this->user->__set('role_id', (int)$data['role']);
                $this->user->__set('password', $data['password']);
                $this->user->__set('email', $data['email']);
                $this->user->save();

                return Helper::getAlertPropsByAction($action, 'Utilisateur', false);
            } else {
                if (empty($errors)) {
                    return Helper::setAlertError('Utilisateur existe déjà');
                }
                return Helper::setAlertErrors($errors);
            }
        }
        return false;

    }
}
