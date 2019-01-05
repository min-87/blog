<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-08-08
 * Time: 15:45
 */

namespace app\controllers;

use app\models\LoginForm;
use app\models\SignupForm;
use app\models\User;
use Yii;
use yii\web\Controller;

class AuthController extends Controller
{
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {//если пользователь является авторизованным
            return $this->goHome();//то перенаправляем его на главную
        }
        //если зашедший является гостем, то приступаем к процессу авторизации этого гостя
        $model = new LoginForm();//создаём экземпляр модели LoginForm, затем выводим форму с полями логина и пароля
        if ($model->load(Yii::$app->request->post()) && $model->login()) {//Yii::$app->request->post() - когда нажали кнопку войти, то тут мы ловим данные из формы; load - и лоудим нашей моделью; $model->login() - и запускаем метод логин
            return $this->goBack();//если ничего из этого не вернуло false, то значит пользователь авторизован и нужно перенаправить его назад
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionSignup()
    {
        $model = new SignupForm();
        if(Yii::$app->request->isPost)//отлавливаем запрос POST
        {
            $model->load(Yii::$app->request->post());//затем наполняем нашу модель пришедшими данными,
            if($model->signup())//после чего вызываем метод регистрации. Если регистрация прошла успешно
            {
                return $this->redirect(['auth/login']);//то перенаправляем на страницу логина
            }
        }
        return $this->render('signup', ['model'=>$model]);
    }

    //тестовый метод, в котором мы будем проводить свои опыты
    public function actionTest(){
        $user = User::findOne(1);//вытащим из базы пользователя
        Yii::$app->user->login($user);//и вызвать у компонента метод логин и стравить пользователя этому методу
        if(Yii::$app->user->isGuest)//если пользователь гость
        {
            echo 'Пользователь гость';
        }
        else
        {
            echo 'Пользователь Авторизован';
        }
    }

}