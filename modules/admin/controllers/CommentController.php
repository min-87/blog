<?php
namespace app\modules\admin\controllers;
use app\models\Comment;
use yii\web\Controller;
class CommentController extends Controller
{
    public function actionIndex()
    {
        $comments = Comment::find()->orderBy('id desc')->all();//сортируем все комменты по id, последние будут стоять первыми

        return $this->render('index',['comments'=>$comments]);
    }
    //экшн удаления
    public function actionDelete($id)
    {
        $comment = Comment::findOne($id);//цепляем id
        if($comment->delete())//и удаляем
        {
            return $this->redirect(['comment/index']);
        }
    }
    //экшн разрешить
    public function actionAllow($id)
    {
        $comment = Comment::findOne($id);
        if($comment->allow())
        {
            return $this->redirect(['index']);
        }
    }
    //экшн запретить
    public function actionDisallow($id)
    {
        $comment = Comment::findOne($id);
        if($comment->disallow())
        {
            return $this->redirect(['index']);
        }
    }
}