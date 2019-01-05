<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 *
 * @property User|null $user This property is read-only.
 *
 */
class LoginForm extends Model
{   //загружаем пришедшие из формы данные в эти свойства модели
    public $email;
    public $password;
    public $rememberMe = true;

    private $_user = false;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['email', 'password'], 'required'],//email и пароль обязательны
            [['email'], 'email'],
            ['rememberMe', 'boolean'],//кнопка запомнить меня должна возвращать булево значение
            ['password', 'validatePassword'],//пароль должен пройти кастомную проверку
        ];
    }

    /**
     * Кастомная проверка пароля
     * Метод возвращает true или false в зависимости от проверки, является ли отправленный из формы пароль идентичным по отношению пароля,
     *который хранится в базе. Проверяет идентичность введённого пароля с тем паролем, что находится в базе у того пользователя, логин которого гость только что ввёл в форму.
     *
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {//спрашивает, нет ли ошибок валидации от предыдущих полей
            $user = $this->getUser();//если нет, то входим и сразу достаём пользователя из базы; $user - итак, тут мы получили пользователя, тоесть есть совпадения введённого логина на одно из тех, что есть в базе

            if (!$user || !$user->validatePassword($this->password)) {//!$user - проверяем не нул ли полученный пользователь; !$user->validatePassword($this->password) - и не возвратил ли метод validatePassword false
                $this->addError($attribute, 'Incorrect username or password.');//Если пользователя с таким именем нет или же пароли не подошли, то возвращаем ошибку "Неправильный логин или пароль", а если все ОК, то возвращаем true
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {// запускается валидация
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600*24*30 : 0);//$app->user - стравляет пользователя компоненту user; login($this->getUser() - передаём пользователя в метод логин и в итоге авторизуем его; $this->rememberMe ? 3600*24*30 : 0 - на какое время авторизовать пользователя, если кнопку rememberMe нажали, то авторизуем на 1 месяц, а иначе 0.
        }
        return false;
    }

    /**
     *
     */
    public function getUser()
    {
        if ($this->_user === false) {//Метод смотрит в своё свойство $_user является ли оно пустым
            $this->_user = User::findByEmail($this->email);//если да, то вызывает кастомный метод findByUsername и передаёт в него логин из формы(username); findByUsername - этот метод в модели User должен произвести поиск по таблице в базе на совпадение поля логин со значением, которое пришло из формы
        }

        return $this->_user;//после чего вернуть его в то место, где этот метод был вызван
    }
}
