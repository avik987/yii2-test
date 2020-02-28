<?php

namespace app\models;

use yii\mongodb\ActiveRecord;

class User extends ActiveRecord
{
    /**
     * @return string the name of the index associated with this ActiveRecord class.
     */
    //admin
    const ADMIN_USER = 1;
    //moderator
    const MODERATOR_USER = 2;
    //other user roles
    const CUSTOM_USER = 3;
    //field for validating user
    public $validate_password;

    public $file;

    public static function collectionName()
    {
        return 'user';
    }

    /**
     * @return array list of attribute names.
     */
    public function attributes()
    {
        return ['_id', 'title', 'email',  'password', 'bio', 'avatar_url','auth_token','role'];
    }

    public function rules()
    {
        return [

            [['email','password'], 'required','on'=>'user_create'],
            [['email','password'], 'required','on'=>'login'],
            [['validate_password','password'], 'required','on'=>'user_create'],
            ['email', 'email'],
            [['title'], 'string','max'=>200],
            ['password', 'compare', 'compareAttribute' => 'validate_password','on'=>'user_create'],
            ['email', 'unique','on'=>'user_create'],
            [['file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg'],
            [['validate_password', 'file', 'title', 'email','password','bio', 'avatar_url', 'auth_token', 'role'], 'safe']
        ];
    }

    public function userDefination($auth_token=false){
        $img_path = '';
        if($this->avatar_url){
            $img_path =  \yii\helpers\Url::base(true).'/uploads/'.$this->avatar_url;
        }
        $result_data = [
            'id'=>(string)$this->_id,
            'title'=>$this->title,
            'bio'=>$this->bio,
            'avatar_url'=>$img_path,

        ];
        if($auth_token){
            $result_data['auth_token'] = $this->auth_token;
        }
        return $result_data;

    }


}
