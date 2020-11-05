<?php
// Serializer helper
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

// Enum for the communication status
abstract class CommStatus {
    const INVALID = 'INVALID';
    const UPDATE_OK = 'UPDATE_OK';
    const STATUS_OK = 'STATUS_OK';
    const STATUS_ERROR = 'STATUS_ERROR';
}

// Enum for driver status
abstract class DriverStatus {
    const OFFLINE = 'OFFLINE'; // off the grid - location not set
    const IDLE = 'IDLE'; // waiting for request
    const INCOMING = 'INCOMING'; // incoming request
    const SERVICING_1 = 'SERVICING_1'; // servicing 1 delivery
    const SERVICING_2 = 'SERVICING_2'; // servicing 2 deliveries
}

// Comm is encapsulated and should only be written to by the server
// notice that field names are REFLECTED in JSON
class Comm implements JsonSerializable {
    use JsonSerializeTrait;
    private $status;
    private $user_id;
    private $friendlyName;
    private $location;

    private $error;

    public function __construct(){
        $this->setStatus(CommStatus::INVALID); // unless marked otherwise on completion it is INVALID
    }

    function setStatus($status) { // valid status
        $this->error = "none";
        $this->status = $status;
    }

    function setUserId($user_id) {
        $this->user_id = $user_id;
    }

    function setFriendlyName($friendlyName) {
        $this->friendlyName = $friendlyName;
    }

    function setLocation($location) {
        $this->location = $location;
    }

    function setError($errorStr){
        $this->error = $errorStr;
        $this->status = CommStatus::STATUS_ERROR;
    }
}
?>