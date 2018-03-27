<?php

namespace Kasperski\WorklogBundle\Command\Result;

interface Result
{
    /**
     * @return boolean
     */
    public function hasErrors();

    /**
     * @return array
     */
    public function getErrors();
}
