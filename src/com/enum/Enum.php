<?php
declare(strict_types=1);
// +----------------------------------------------------------------------
// | CodeEngine
// +----------------------------------------------------------------------
// | Copyright 艾邦
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: TaoGe <liangtao.gz@foxmail.com>
// +----------------------------------------------------------------------
// | Version: 2.0 2021/4/19 10:49
// +----------------------------------------------------------------------
namespace com\enum;

use JetBrains\PhpStorm\Pure;
use ReflectionClass;
use UnexpectedValueException;

/**
 * Abstract class that enables creation of PHP enums. All you
 * have to do is extend this class and define some constants.
 * Enum is an object with value from on of those constants
 * (or from on of superclass if any). There is also
 * __default constat that enables you creation of object
 * without passing enum value.
 * @author Marijan Šuflaj <msufflaj32@gmail.com&gt
 * @link   http://php4every1.com
 */
abstract class Enum
{

    /**
     * Constant with default value for creating enum object
     */
    const __default = null;

    private mixed $value;

    private bool $strict;

    private static array $constants = array();

    /**
     * Returns list of all defined constants in enum class.
     * Constants value are enum values.
     * @param bool $includeDefault If true, default value is included into return
     * @return array Array with constant values
     */
    public function getConstList(bool $includeDefault = false): array
    {
        $class = get_class($this);
        if (!array_key_exists($class, self::$constants)) {
            self::populateConstants();
        }
        return $includeDefault ? array_merge(self::$constants[__CLASS__], array(
            "__default" => self::__default
        )) : self::$constants[__CLASS__];
    }

    /**
     * Creates new enum object. If child class overrides __construct(),
     * it is required to call parent::__construct() in order for this
     * class to work as expected.
     * @param mixed|null $initialValue Any value that is exists in defined constants
     * @param bool       $strict       If set to true, type and value must be equal
     * @throws UnexpectedValueException If value is not valid enum value
     */
    public function __construct(mixed $initialValue = null, bool $strict = true)
    {
        $class = get_class($this);
        if (!array_key_exists($class, self::$constants)) {
            self::populateConstants();
        }
        if ($initialValue === null) {
            $initialValue = self::$constants[$class]["__default"];
        }
        $temp = self::$constants[$class];
        if (!in_array($initialValue, $temp, $strict)) {
            throw new UnexpectedValueException("Value is not in enum " . $class);
        }
        $this->value  = $initialValue;
        $this->strict = $strict;
    }

    private function populateConstants()
    {
        $class           = get_class($this);
        $r               = new ReflectionClass($class);
        $constants       = $r->getConstants();
        self::$constants = array(
            $class => $constants
        );
    }

    /**
     * Returns string representation of an enum. Defaults to
     * value casted to string.
     * @return string String representation of this enum's value
     */
    public function __toString()
    {
        return (string)$this->value;
    }

    /**
     * getValue
     * @return mixed
     * @author TaoGe <liangtao.gz@foxmail.com>
     * @date   2021/6/1 11:39
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * Checks if two enums are equal. Only value is checked, not class type also.
     * If enum was created with $strict = true, then strict comparison applies
     * here also.
     * @return bool True if enums are equal
     */
    #[Pure] public function equals($object): bool
    {
        if (!($object instanceof Enum)) {
            return false;
        }
        return $this->strict ? ($this->value === $object->getValue())
            : ($this->value == $object->getValue());
    }
}
