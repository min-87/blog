<?php
namespace app\models;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;
class ImageUpload extends Model{

    public $image;
    //Правила валидации, чтоб можно было загружать только картинки
    public function rules()
    {
        return [
            [['image'], 'required'],//атрибут image обязателен к заполнению
            [['image'], 'file', 'extensions' => 'jpg,png']//указываем атрибуту, чтоб принимал файлы только формата jpg или png
        ];
    }
    // метод загрузки картинки на сервер
    public function uploadFile(UploadedFile $file, $currentImage)
    {
        $this->image = $file;
        if($this->validate())
        {
            $this->deleteCurrentImage($currentImage);// удаляем текущую картинку
            return $this->saveImage();//возвращаем название картинки, чтоб передать его модели статьи, чтобы та в свою очередь сохранила её имя в базе
        }
    }
    private function getFolder()
    {
        return Yii::getAlias('@web') . 'uploads/';// возвращает папку, в которой сохранена картинка
    }
    //метод, который делает название картинки уникальным
    private function generateFilename()
    {
        return strtolower(md5(uniqid($this->image->baseName)) . '.' . $this->image->extension);
    }
    //метод, который удаляет текущую картинку
    public function deleteCurrentImage($currentImage)
    {
        if($this->fileExists($currentImage))//заменяем старую картинку только в том случае, если она есть на сервере
        {
            unlink($this->getFolder() . $currentImage);//заменяем текущей картинкой старую картинку
        }
    }
    //проверка на существование файла
    public function fileExists($currentImage)
    {
        if(!empty($currentImage) && $currentImage != null)//проверка на нуль и пустое значение, передаваемое переменной
        {
            return file_exists($this->getFolder() . $currentImage);
        }
    }
    //метод загрузки картинки
    public function saveImage()
    {
        $filename = $this->generateFilename();//делаем название картики уникальным
        $this->image->saveAs($this->getFolder() . $filename);// указываем, в какую папку будем сохранять картинку
        return $filename;// возвращаем название файла
    }
}