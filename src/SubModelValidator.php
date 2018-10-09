<?php
namespace matrozov\yii2subModelValidator;

use Yii;
use yii\base\Model;
use yii\validators\Validator;

/**
 * Class SubModelValidator
 * @package matrozov\yii2subModelValidator
 *
 * @property Model $class
 * @property boolean $strictObject
 */
class SubModelValidator extends Validator
{
    /**
     * @var Model
     */
    public $class;

    /**
     * @var bool
     */
    public $strictObject = false;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        if ($this->message === null) {
            $this->message = Yii::t('yii', '{attribute} is invalid.');
        }
    }

    /**
     * @param mixed $value
     *
     * @return array|null
     * @throws
     */
    public function validateValue($value)
    {
        if (($this->strictObject && !is_object($value)) || (!$this->strictObject && !is_object($value) && !is_array($value) && !($value instanceof \ArrayAccess))) {
            return [$this->message, []];
        }

        $model = Yii::createObject($this->class);

        if (!$model->load($value, '') || !$model->validate()) {
            return [reset(reset($model->errors)), []];
        }

        return null;
    }
}