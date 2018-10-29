<?php
namespace matrozov\yii2subModelValidator;

use Yii;
use yii\base\Model;
use yii\validators\Validator;

/**
 * Class SubModelValidator
 * @package matrozov\yii2subModelValidator
 *
 * @property Model $model
 * @property boolean $strictObject
 */
class SubModelValidator extends Validator
{
    const SEPARATOR = '->';

    /**
     * @var Model
     */
    public $model;

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
     * @param Model  $model
     * @param string $attribute
     *
     * @return array|void
     * @throws
     */
    public function validateAttribute($model, $attribute)
    {
        $value = $model->$attribute;

        if (($this->strictObject && !is_object($value)) || (!$this->strictObject && !is_object($value) && !is_array($value) && !($value instanceof \ArrayAccess))) {
            $this->addError($model, $attribute, $this->message, []);
            return;
        }

        /** @var Model $object */
        $object = Yii::createObject(['class' => $this->model]);

        if (!$object->load($value, '') || !$object->validate()) {
            if ($object->hasErrors()) {
                foreach ($object->errors as $field => $errors) {
                    foreach ($errors as $error) {
                        $model->addError($attribute . self::SEPARATOR . $field, $error);
                    }
                }
            }
            else {
                $this->addError($model, $attribute, $this->message, []);
            }

            return;
        }

        $model->$attribute = $object;
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

        /** @var Model $object */
        $object = Yii::createObject(['class' => $this->model]);

        if (!$object->load($value, '') || !$object->validate()) {
            return [reset(reset($object->errors)), []];
        }

        return null;
    }
}