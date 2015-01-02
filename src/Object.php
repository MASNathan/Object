<?php

namespace MASNathan;

//phpunit --bootstrap vendor/autoload.php tests/ObjectTest

/**
 * @todo  add events -> onChange() every time something is setted
 * @todo  add events -> on[Property]Change() every time the property is setted
 * @todo  add clone function
 * @todo  add (array) casting hendler, check ArrayObject
 */
class Object
    implements \IteratorAggregate, \ArrayAccess, \Countable, \Serializable, \JsonSerializable
{

    protected $data;

    public function __construct($data = array())
    {
        $this->data = new \StdClass;
        foreach ($data as $key => $value) {
            if (is_array($value) || (is_object($value) && get_class($value) == 'stdClass')) {
                $value = new self($value);
            }

            $this->data->$key = $value;
        }
    }

    public function __get($key)
    {
        return $this->data->$key;
    }

    public function __set($key, $value)
    {
        $this->data->$key = $value;
    }

    public function __call($alias, array $args = array())
    {
        preg_match_all('/[A-Z][^A-Z]*/', $alias, $parts);
        
        $key = strtolower(implode('_', $parts[0]));

        
        // Returns a value from a property e.g.: $object->getProperty() -> returns 'value';
        if (strpos($alias, 'get') === 0 && !empty($key)) {
            return isset($this->data->$key) ? $this->data->$key : null;
        }
        // Sets a value to a property and returns it'self e.g.: $object->setProperty('value') -> returns Object class
        if (strpos($alias, 'set') === 0 && !empty($key)) {
            $value = reset($args);

            if (is_array($value)) {
                $value = new self($value);
            }

            $this->data->$key = $value;
            return $this;
        }
        // Unsets a property if it's setted
        if (strpos($alias, 'unset') === 0 && !empty($key)) {
            if (isset($this->data->$key)) {
                unset($this->data->$key);
            }
            return $this;
        }
        // Returns boolean e.g.: isActive(), isVisible()
        if (strpos($alias, 'is') === 0 && !empty($key)) {
            $value = reset($args);
            // e.g.: isRole('admin'), isEncoding('base64')
            if ($value) {
                return isset($this->data->$key) ? $this->data->$key == $value : false;
            }
            return isset($this->data->$key) ? (bool) $this->data->$key : false;
        }
        // If the called function is not a set/get/is kind of thing,
        // we check if its callable and return it's execution result
        if (isset($this->data->$alias) && is_callable($this->data->$alias)) {
            return call_user_func_array($this->data->$alias, $args);
        }
        
    }

    /**
     * Serializes the object to a value that can be serialized natively by json_encode().
     * @overrides \JsonSerializable::jsonSerialize
     * @return \StdClass Returns data which can be serialized by json_encode(), which is a value of any type other than a resource.
     */
    public function jsonSerialize()
    {
        return $this->data;
    }

    /**
     * Should return the string representation of the object
     * @overrides \Serializable::serialize
     * @return string Returns the string representation of the object or NULL
     */
    public function serialize()
    {
        return serialize($this->data);
    }

    /**
     * Called during unserialization of the object
     * @overrides \Serializable::unserialize
     * @param  string $serializedData The string representation of the object
     * @return null The return value from this method is ignored.
     */
    public function unserialize($serializedData)
    {
        $this->data = unserialize($serializedData);
    }

    /**
     * Returns an external iterator
     * @overrides \IteratorAggregate::getIterator
     * @return \ArrayIterator
     */
    public function getIterator() {
        return new \ArrayIterator($this->data);
    }
    
    public function offsetSet($offset, $value)
    {
        if (!is_null($offset)) {
            $this->data->$offset = $value;
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->data->$offset);
    }

    public function offsetUnset($offset)
    {
        unset($this->data->$offset);
    }

    public function offsetGet($offset)
    {
        return isset($this->data->$offset) ? $this->data->$offset : null;
    }

    public function rewind()
    {
        return \reset($this->data);
    }
  
    public function current()
    {
        return \current($this->data);
    }
  
    public function key() 
    {
        return \key($this->data);
    }
  
    public function next() 
    {
        return \next($this->data);
    }
  
    public function valid()
    {
        $key = key($this->data);
        return ($key !== null && $key !== false);
    }

    public function count()
    {
        return count($this->data);
    }

    public function toArray()
    {
        return (array) $this->data;
    }

}