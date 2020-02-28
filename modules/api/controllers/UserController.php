<?php

namespace app\modules\api\controllers;

use Yii;
use yii\rest\ActiveController;
use app\models\User;
use yii\web\UploadedFile;


class UserController extends ActiveController
{
    public $modelClass = 'app\models\User';

    public function actions()
    {
        $actions = parent::actions();

        unset($actions['create']);
        unset($actions['index']);
        unset($actions['view']);
        unset($actions['update']);
        return $actions;
    }

    public function actionIndex(){
        $data = Yii::$app->request->post();
        $limit = 10;
        $offset = 0;
        if(isset($data['limit'])){
            $limit = $data['limit'];
        }
        if(isset($data['offset'])){
            $offset = $data['offset'];
        }
        $model = new User();
        $result = $model->find()->where([])->limit($limit)->offset($offset)->all();
        $response_data = [];
        foreach($result as $key=>$value){

            $response_data[] = $value->userDefination();
        }
        $response = ['success'=>true, 'data'=>['model'=>$response_data],'errors'=>[]];
        return $response;
    }

    public function actionCreate(){

        $data = Yii::$app->request->post();

        $model = new User();
        $model->scenario = 'user_create';
        $model->setAttributes($data);


        if($model->validate()){
            $model->password = Yii::$app->security->generatePasswordHash($model->password);
            $model->role = User::CUSTOM_USER;
            $model->auth_token = Yii::$app->security->generateRandomString();
            $model->title = '';
            $model->bio = '';
            $model->avatar_url = '';
            $model->save(false);
            $userDefination = $model->userDefination(true);
            $response = ['success'=>true, 'data'=>$userDefination,'errors'=>[]];
        }else{
            $response = ['success'=>false, 'data'=>[],'errors'=>$model->errors];
        }
        return $response;
    }

    public function actionUpdate(){
        $headers = Yii::$app->request->headers;
        $auth_token = $headers->get('auth_token');

        if ($auth_token) {
            $request = Yii::$app->request;

            $model = User::findOne(['auth_token'=>$auth_token]);
            if(!$model){
                return ['success'=>false, 'data'=>[],'errors'=>[]];
            }
            $model->setAttributes($request->post());
            $result = $model->attributes;
            $result['_id'] = (string)$model->_id;

            $model->avatar_url = $this->base64ToImage($model->file);

            if($model->save(true, ['title','bio'])){



                $response = ['success'=>true, 'data'=>$model->userDefination(),'errors'=>[]];
            }else{
                $response = ['success'=>true, 'data'=>[],'errors'=>$model->errors];

            }

            return $response;
        }else{
            return ['success'=>false,'data'=>[] , 'errors'=>['access_token'=>['Token Missing']]];
        }

    }



    public function actionView($id){

        if(!$id){
            return['success'=>false, 'data'=>[],'errors'=>[]];
        }
        $model = User::findOne($id);
        if(!$model){
           return ['success'=>false, 'data'=>[],'errors'=>[]];
        }
        $result = $model->userDefination();

        $response = ['success'=>true, 'data'=>$result,'errors'=>[]];
        return $response;
    }

    public function actionLogin(){

        $data = Yii::$app->request->post();
        $model = new User();
        $model->scenario = 'login';
        $model->setAttributes($data);
        if($model->validate(['email','password'])){
           $user =  $model->find()->where(['email'=>$data['email']])->one();
           if(!$user){
               $response = ['success'=>false, 'data'=>[],'errors'=>['email'=>['Email not found']]];
           }else if(!Yii::$app->security->validatePassword($model->password,$user->password)){
               $response = ['success'=>false, 'data'=>[],'errors'=>['email'=>['Wrong password']]];
           }else{
               $response = ['success'=>true, 'data'=>$user->userDefination(true),'errors'=>[]];
           }
        }else{
            $response = ['success'=>false, 'data'=>[],'errors'=>$model->errors];
        }
        return $response;
    }

    private function base64ToImage($base64_string) {
        $data = explode(',', $base64_string);
        $img_extension = explode('/',$data[0]);
        $img_extension = explode(';',$img_extension[1]);
        $ext = $img_extension[0];
        $file_name = Yii::$app->security->generateRandomString(16).'.'.$ext;
        $file = fopen('uploads/'.$file_name, "wb");
        fwrite($file, base64_decode($data[1]));
        fclose($file);

        return $file_name;
    }
}