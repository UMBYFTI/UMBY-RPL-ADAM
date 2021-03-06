<?php
/**
* SiteController
* Handle SiteController
* Copyright (c) 2012, Ommu Platform (ommu.co). All rights reserved.
* version: 0.0.1
* Reference start
*
* TOC :
*	Index
*	View
*	Feed
*
*	Dialog
*	Default
*
*	LoadModel
*	performAjaxValidation
*
* @author Putra Sudaryanto <putra.sudaryanto@gmail.com>
* @copyright Copyright (c) 2012 Ommu Platform (ommu.co)
* @link http://company.ommu.co
* @contact (+62)856-299-4114
*
*----------------------------------------------------------------------------------------------------------
*/

class SiteController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	//public $layout='//layouts/column2';
	public $defaultAction = 'index';

	/**
	 * Initialize admin page theme
	 */
	public function init() 
	{
		if(ProjectSetting::getInfo('permission') == 1) {
			$arrThemes = Utility::getCurrentTemplate('public');
			Yii::app()->theme = $arrThemes['folder'];
			$this->layout = $arrThemes['layout'];
		} else {
			$this->redirect(Yii::app()->createUrl('site/index'));
		}
	}

	/**
	 * @return array action filters
	 */
	public function filters() 
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			//'postOnly + delete', // we only allow deletion via POST request
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules() 
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view','feed'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array(),
				'users'=>array('@'),
				'expression'=>'isset(Yii::app()->user->level)',
				//'expression'=>'isset(Yii::app()->user->level) && (Yii::app()->user->level != 1)',
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array(),
				'users'=>array('@'),
				'expression'=>'isset(Yii::app()->user->level) && (Yii::app()->user->level == 1)',
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array(),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
	
	/**
	 * Lists all models.
	 */
	public function actionIndex() 
	{
		$setting = ProjectSetting::model()->findByPk(1,array(
			'select' => 'meta_description, meta_keyword',
		));

		$criteria=new CDbCriteria;
		$criteria->condition = 'publish = :publish';
		$criteria->params = array(':publish'=>1);
		$criteria->order = 'creation_date DESC';

		$dataProvider = new CActiveDataProvider('Projects', array(
			'criteria'=>$criteria,
			'pagination'=>array(
				'pageSize'=>10,
			),
		));

		$this->pageTitle = 'Projects';
		$this->pageDescription = $setting->meta_description;
		$this->pageMeta = $setting->meta_keyword;
		$this->render('front_index',array(
			'dataProvider'=>$dataProvider,
		));
	}
	
	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id) 
	{
		$setting = ProjectSetting::model()->findByPk(1,array(
			'select' => 'meta_keyword',
		));

		$model=$this->loadModel($id);
			
		$this->dialogDetail = true;
		$this->dialogGroundUrl = Yii::app()->controller->createUrl('site/index');

		$this->pageTitle = $model->title;
		$this->pageDescription = Utility::shortText(Utility::hardDecode($model->body),300);
		$this->pageMeta = ProjectTag::getKeyword($setting->meta_keyword, $id);
		$this->pageImage = $model->media_id != 0 ? Yii::app()->request->baseUrl.'/public/project/'.$id.'/'.$model->cover->media : '';
		$this->render('front_view',array(
			'model'=>$model,
		));
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionFeed() 
	{
		$setting = ProjectSetting::model()->findByPk(1,array(
			'select' => 'meta_description, meta_keyword',
		));

		$model = Projects::model()->findAll(array(
			'condition' => 'publish = :publish',
			'params' => array(
				':publish' => 1,
			),
			'limit' => 10,
			'order' => 'project_id DESC',
		));

		Yii::import('application.extensions.feed.*');
		// RSS 2.0 is the default type
		$feed = new EFeed();
		 
		$feed->title= 'Project';
		$feed->description = $setting->meta_description;

		//$feed->setImage('Testing RSS 2.0 EFeed class','http://www.ramirezcobos.com/rss', 'http://www.yiiframework.com/forum/uploads/profile/photo-7106.jpg');		 
		//$feed->addChannelTag('language', 'en-us');
		$feed->addChannelTag('pubDate', date(DATE_RSS, time()));
		$feed->addChannelTag('link', 'http://'.$_SERVER['HTTP_HOST'].Yii::app()->controller->createUrl('feed'));
		// * self reference
		//$feed->addChannelTag('atom:link','http://www.ramirezcobos.com/rss/');

		if($model != null) {
			foreach($model as $key => $val) {
				$item = $feed->createNewItem();		 
				$item->title = $val->title;
				$item->link = 'http://'.$_SERVER['HTTP_HOST'].Yii::app()->controller->createUrl('view', array('id'=>$val->product_id, 't'=>Utility::getUrlTitle($val->title)));
				$item->date = Utility::dateFormat($val->creation_date, true);
				$item->description = Utility::shortText(Utility::hardDecode($val->body),300);
				// this is just a test!!
				//$item->setEncloser('http://www.tester.com', '1283629', 'audio/mpeg');		 
				$item->addTag('author', $val->user->displayname);
				$item->addTag('guid', 'mailto:'.$val->user->email, array('isPermaLink'=>'true'));
				$feed->addItem($item);
			}
		}

		$feed->generateFeed();
		Yii::app()->end();

	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id) 
	{
		$model = Projects::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404, 'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model) 
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='projects-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
