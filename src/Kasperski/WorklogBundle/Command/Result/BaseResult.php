<?php

namespace Kasperski\WorklogBundle\Command\Result;

use Kasperski\WorklogBundle\Traits\ErrorManipulationTrait;

abstract class BaseResult implements Result
{
    use ErrorManipulationTrait;
}
