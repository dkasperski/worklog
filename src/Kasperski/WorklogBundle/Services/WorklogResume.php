<?php

namespace Kasperski\WorklogBundle\Services;

use Kasperski\WorklogBundle\Command\Result\WorklogResult;
use Kasperski\WorklogBundle\Command\WorklogCommandDispatcher;
use Kasperski\WorklogBundle\Command\WorklogCommandFactory;

class WorklogResume {

    /**
     * @var WorklogCommandDispatcher
     */
    protected $worklogCommandDispatcher;

    /**
     * @var WorklogCommandFactory
     */
    protected $worklogCommandFactory;

    /**
     * @var array
     */
    protected $errors = [];

    /**
     * @param WorklogCommandDispatcher $worklogCommandDispatcher
     * @param WorklogCommandFactory $worklogCommandFactory
     */
    public function __construct(
        WorklogCommandDispatcher $worklogCommandDispatcher,
        WorklogCommandFactory $worklogCommandFactory
    ) {
        $this->worklogCommandDispatcher = $worklogCommandDispatcher;
        $this->worklogCommandFactory = $worklogCommandFactory;
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
     * @return WorklogCreator
     */
    public function setErrors(array $errors)
    {
        $this->errors = $errors;
        return $this;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function resume()
    {
        $worklogCommand = $this->worklogCommandFactory->create();
        /** @var $worklogResult WorklogResult */
        $worklogResult = $this->worklogCommandDispatcher->resume($worklogCommand);

        if ($worklogResult->hasErrors()) {
            $this->setErrors($worklogResult->getErrors());
            return false;
        }

        return true;
    }
}
