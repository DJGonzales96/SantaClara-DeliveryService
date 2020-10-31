<?php
trait JsonSerializeTrait
{
    function jsonSerialize()
    {
        $reflect = new \ReflectionClass($this);
        $props   = $reflect->getProperties(\ReflectionProperty::IS_STATIC | \ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED | \ReflectionProperty::IS_PRIVATE);

        $propsIterator = function() use ($props) {
            foreach ($props as $prop) {
                yield $prop->getName() => $this->{$prop->getName()};
            }
        };
        return iterator_to_array($propsIterator());
    }
}


abstract class CommStatus {
    const INVALID = 'INVALID';
    const READY = 'READY';
}

abstract class DriverStatus {
    const OFFLINE = 'OFFLINE'; // off the grid - location not set
    const IDLE = 'IDLE'; // waiting for request
    const INCOMING = 'INCOMING'; // incoming request
    const SERVICING_1 = 'SERVICING_1'; // servicing 1 delivery
    const SERVICING_2 = 'SERVICING_2'; // servicing 2 deliveries
}

class Comm implements JsonSerializable {
    use JsonSerializeTrait;
    private $status;

    public function __construct(){
        $this->setStatus(CommStatus::INVALID);
    }

    function setStatus($status) {
        $this->status = $status;
    }

}
?>