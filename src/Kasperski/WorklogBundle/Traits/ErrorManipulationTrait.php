<?php

namespace Kasperski\WorklogBundle\Traits;

trait ErrorManipulationTrait
{
    /**
     * @var array
     */
    protected $errors = [];

    /**
     * @param array $errors
     */
    public function __construct(array $errors = [])
    {
        $this->setErrors($errors);
    }

    /**
     * @return bool
     */
    public function hasErrors()
    {
        return count($this->errors) > 0;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param array $errors
     */
    protected function setErrors(array $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'errors' => $this->getErrors(),
        ];
    }
}
