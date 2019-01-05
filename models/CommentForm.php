<?php
namespace app\models;
use Yii;
use yii\base\Model;
class CommentForm extends Model
{
    public $comment;

    public function rules()
    {
        return [
            [['comment'], 'required'],//поле comment обязательное
            [['comment'], 'string', 'length' => [3,250]]//ограничим к-ство символов
        ];
    }
    //создаём саму модель комментария и записываем в неё все нужные данные
    public function saveComment($article_id)
    {
        $comment = new Comment;
        $comment->text = $this->comment;//текст
        $comment->user_id = Yii::$app->user->id;//id пользователя
        $comment->article_id = $article_id;//id текущей статьи
        $comment->status = 0;//если статус 0, то комментарий ждёт подтверждения
        $comment->date = date('Y-m-d');//добавляем дату
        return $comment->save();
    }
}