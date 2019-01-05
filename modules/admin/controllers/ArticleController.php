<?php
namespace app\modules\admin\controllers;
use app\models\Category;
use app\models\ImageUpload;
use app\models\Tag;
use Yii;
use app\models\Article;
use app\models\ArticleSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
/**
 * ArticleController implements the CRUD actions for Article model.
 */
class ArticleController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }
    /**
     * Lists all Article models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ArticleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    /**
     * Displays a single Article model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }
    /**
     * Creates a new Article model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Article();
        if ($model->load(Yii::$app->request->post()) && $model->saveArticle()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }
    /**
     * Updates an existing Article model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->saveArticle()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }
    /**
     * Deletes an existing Article model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }
    /**
     * Finds the Article model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Article the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Article::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    public function actionSetImage($id)
    {
        $model = new ImageUpload;// создаётся экземпляр модели, которая занимается только загрузкой картинки
        if (Yii::$app->request->isPost)//Отлавливаем нажатие кнопки
        {
            $article = $this->findModel($id);// вытащим статью из базы по id
            $file = UploadedFile::getInstance($model, 'image');//получаем файл
            if($article->saveImage($model->uploadFile($file, $article->image)))//$model->uploadFile($file, $article->image) - передаём файл и название картинки в метод uploadFile модели ImageUpload; $article->saveImage - метод принимает название файла, которое нужно сохранить. А название файла нам вернёт метод uploadFile, название передаём методу статьи saveImage для сохранения в базу
            {
                return $this->redirect(['view', 'id'=>$article->id]);//при сохранении в базу направим пользователя обратно на страницу view
            }
        }

        return $this->render('image', ['model'=>$model]);//вид с формой, где мы будем отображать картинку
    }
    public function actionSetCategory($id){
        $article = $this->findModel($id);//получаем статью по id
        $selectedCategory = $article->category->id;
        $categories = ArrayHelper::map(Category::find()->all(), 'id', 'title');//ArrayHelper::map - создаёт массив списка категорий; Category::find()->all() - список категорий из базы; 'id', 'title' - названия полей
        if(Yii::$app->request->isPost)//отлавливаем нажатие кнопки(если форма отправлена)
        {
            $category = Yii::$app->request->post('category');//в переменную category передаём значение из инпута category
            if($article->saveCategory($category))//передаём методу сохранения категории выбранную категорию,
            {
                return $this->redirect(['view', 'id'=>$article->id]);//после чего отправим пользователя на страницу view
            }
        }
        return $this->render('category', [//затем все эти данные передаём в вид
            'article'=>$article,//передаём модель статей
            'selectedCategory'=>$selectedCategory,//передаём текущую категорию виду
            'categories'=>$categories
        ]);
    }
    //метод добавления тегов
    public function actionSetTags($id)
    {
        $article = $this->findModel($id);//вытаскиваем статью
        $selectedTags = $article->getSelectedTags(); //
        $tags = ArrayHelper::map(Tag::find()->all(), 'id', 'title');//нам нужен список всех тегов
        if(Yii::$app->request->isPost)//если форму отправили
        {
            $tags = Yii::$app->request->post('tags');// то в переменную $tags кладём все значения из инпута 'tags'
            $article->saveTags($tags);//сохранение выбранных тегов
            return $this->redirect(['view', 'id'=>$article->id]);//перенаправим пользователя на страницу view
        }

        return $this->render('tags', [//передаём переменные в вид
            'selectedTags'=>$selectedTags,
            'tags'=>$tags
        ]);
    }

}