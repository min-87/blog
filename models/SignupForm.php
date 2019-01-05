<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-08-09
 * Time: 10:28
 */

namespace app\models;


use yii\base\Model;

class SignupForm extends Model
{
    public $name;
    public $email;
    public $password;
    //правила валидации
    public function rules()
    {
        return [
            [['name','email','password'], 'required'],//все поля будут обязательными
            [['name'], 'string'],//имя будет строкой
            [['email'], 'email'],//email должен совпадать с форматом почты
            [['email'], 'unique', 'targetClass'=>'app\models\User', 'targetAttribute'=>'email']//email должен быть уникальным(нельзя использовать один и тот же email для регистрации). Для этого мы указываем, по отношению к какой модели какой таблицы он должен быть уникальным и соотвественно указываем нужное поле
        ];
    }

    public function signup()
    {
        if($this->validate())//удостоверимся, что все поля прошли валидацию
        {
            $user = new User();//затем создаём пустую модель юзера
            $user->attributes = $this->attributes;//и заполняем её, передав атрибуты текущей модели
            return $user->create();//и вызываем метод create
        }
    }

}