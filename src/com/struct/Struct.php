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
// | Version: 2.0 2021/5/10 9:38
// +----------------------------------------------------------------------

namespace com\struct;

use ArrayAccess;
use com\enum\Enum;
use com\log\Log;
use Error;
use JsonSerializable;
use ReflectionClass;
use ReflectionException;
use Throwable;

abstract class Struct implements JsonSerializable, ArrayAccess
{

    /**
     * Struct constructor.
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $ref = new ReflectionClass($this);
        foreach ($data as $key => $value) {
            if (!$ref->hasProperty($key)) {
                $key = parse_name($key, 1, false);
            }
            if ($ref->hasProperty($key)) {
                $property    = $ref->getProperty($key);
                $method_name = 'set' . parse_name($property->getName(), 1);
                if ($ref->hasMethod($method_name)) {
                    try {
                        $ref->getmethod($method_name)->invoke($this, $value);
                    } catch (ReflectionException $e) {
                        Log::warning((string)$e);
                    }
                } else {
                    $property->setAccessible(true);
                    $property->setValue($this, $value);
                }
            }
        }
    }

    /**
     * 转换当前对象为Array数组
     * @param bool $style     命名风格 默认：true; 选项：false = Java风格 true = C风格
     * @param bool $objEscape 对象是否转为基本数据类型 默认：true;
     * @return array
     * @author TaoGe <liangtao.gz@foxmail.com>
     * @date   2021/7/19 10:45
     */
    public function toArray(bool $style = true, bool $objEscape = true): array
    {
        $ref   = new ReflectionClass($this);
        $array = [];
        foreach ($ref->getProperties() as $property) {
            $key = $style ? parse_name($property->getName()) : $property->getName();
            try {
                $method_name = 'get' . parse_name($property->getName(), 1);
                if ($ref->hasMethod($method_name)) {
                    $value = $ref->getmethod($method_name)->invoke($this);
                } else {
                    $property->setAccessible(true);
                    $value = $property->getValue($this);
                }
            } catch (Throwable $e) {
                Log::warning((string)$e);
                $value = null;
            }
            if ($objEscape) {
                if ($value instanceof Struct) {
                    $array[$key] = $value->toArray($style, $objEscape);
                } else if ($value instanceof Enum) {
                    $array[$key] = $value->getValue();
                } else {
                    $array[$key] = $value;
                }
            } else {
                $array[$key] = $value;
            }
        }
        return $array;
    }

    /**
     * 转换当前对象为JSON字符串
     * @param int $options
     * @return string
     * @author TaoGe <liangtao.gz@foxmail.com>
     * @date   2021/5/10 11:26
     */
    public function toJson(int $options = JSON_UNESCAPED_UNICODE): string
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * 返回能被 json_encode() 序列化的数据， 这个值可以是除了 resource 外的任意类型。
     * @return array
     * @author TaoGe <liangtao.gz@foxmail.com>
     * @date   2021/5/10 11:30
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * 检查一个偏移位置是否存在
     * @param mixed $offset
     * @return bool
     * @author TaoGe <liangtao.gz@foxmail.com>
     * @date   2021/5/10 9:36
     */
    public function offsetExists($offset): bool
    {
        return property_exists($this, $offset);
    }

    /**
     * 获取一个偏移位置的值
     * @param mixed $offset
     * @return mixed
     * @author TaoGe <liangtao.gz@foxmail.com>
     * @date   2021/5/10 9:37
     */
    public function offsetGet($offset): mixed
    {
        return $this->$offset;
    }

    /**
     *  设置一个偏移位置的值
     * @param mixed $offset
     * @param mixed $value
     * @author TaoGe <liangtao.gz@foxmail.com>
     * @date   2021/5/10 9:37
     */
    public function offsetSet($offset, $value): void
    {
        $this->$offset = $value;
    }

    /**
     * 复位一个偏移位置的值
     * @param mixed $offset
     * @author TaoGe <liangtao.gz@foxmail.com>
     * @date   2021/5/10 9:37
     */
    public function offsetUnset($offset): void
    {
        unset($this->$offset);
    }

    /**
     * __toString
     * @return string
     * @author TaoGe <liangtao.gz@foxmail.com>
     * @date   2021/5/10 11:27
     */
    public function __toString(): string
    {
        return $this->toJson();
    }

    public function __call(string $name, array $arguments)
    {
        $prefix = substr($name, 0, 3);
        if (!in_array($prefix, ['get', 'set'])) {
            throw new Error('Call to undefined method ' . __METHOD__ . '()');
        }
        $ref = new ReflectionClass($this);
        $key = lcfirst(substr($name, 3));
        if (!$ref->hasProperty($key)) {
            throw new Error('Call to undefined method ' . __CLASS__ . '::' . $name . '()');
        }
        $property = $ref->getProperty($key);
        $property->setAccessible(true);
        if ($prefix === 'get') {
            return $property->getValue($this);
        }
        $property->setValue($this, $arguments[0]);
    }
}
