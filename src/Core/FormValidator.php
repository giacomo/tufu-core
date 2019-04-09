<?php
/**
 * Project      tufu
 * @author      Giacomo Barbalinardo <info@ready24it.eu>
 * @copyright   2017
 */

namespace Tufu\Core;

use Respect\Validation\Rules\AbstractRule;

class FormValidator
{
    protected $error = array();

    public function validate($parameterBag, $rules = array())
    {
        foreach ($rules as $parameter => $rule) {
            if (!array_key_exists($parameter, $parameterBag)) {
                $this->addError($parameter);
            } else {
                /** @var AbstractRule $validator $validator */
                $className = 'Respect\\Validation\\Rules\\' . $rule;
                $validator = new $className();

                if (!$validator->validate($parameterBag[$parameter])) {
                    $this->addError($parameter, $rule);
                }
            }
        }
    }

    public function isValid()
    {
        return count($this->error) === 0;
    }

    private function addError($parameter, $name = 'Defined not passed.')
    {
        $this->error[$parameter] = $name;
    }

    public function getErrors()
    {
        return $this->error;
    }
}