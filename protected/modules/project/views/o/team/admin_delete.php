<?php
/**
 * @var $this TeamController
 * @var $model ProjectTeam
 *
 * @author Putra Sudaryanto <putra@sudaryanto.id>
 * @copyright Copyright (c) 2014 Ommu Platform (ommu.co)
 * @link http://company.ommu.co
 * @contact (+62)856-299-4114
 *
 */

$this->breadcrumbs=array(
	'Project Teams'=>array('manage'),
	'Delete',
);
?>

<?php $form=$this->beginWidget('application.components.system.OActiveForm', array(
	'id'=>'project-team-form',
	'enableAjaxValidation'=>true,
	//'htmlOptions' => array('enctype' => 'multipart/form-data')
)); ?>

	<div class="dialog-content">
		<?php echo 'Are you sure you want to delete this item?';?>
		
	</div>
	<div class="dialog-submit">
		<?php echo CHtml::submitButton('Delete', array('onclick' => 'setEnableSave()')); ?>
		<?php echo CHtml::button('Cancel', array('id'=>'closed')); ?>
	</div>
	
<?php $this->endWidget(); ?>
