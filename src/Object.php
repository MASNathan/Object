<?php

namespace MASNathan;

//phpunit --bootstrap vendor/autoload.php tests/ObjectTest

/**
 * @todo  add events -> onChange() every time something is setted
 * @todo  add events -> on[Property]Change() every time the property is setted
 * @todo  add clone function
 * @todo  add (array) casting hendler, check ArrayObject
 */
class Object implements \IteratorAggregate, \ArrayAccess, \Countable, \Serializable, \JsonSerializable
{
    /**
     * Data holder
     * @var \StdClass
     */
    protected $data;

    public function __construct($data = array())
    {
        $this->data = new \StdClass;
        foreach ($data as $key => $value) {
            // If a child is an associative array or an stdClass we convert it as well
            if ((is_array($value) && (bool) count(array_filter(array_keys($value), 'is_string'))) || (is_object($value) && get_class($value) == 'stdClass')) {
                $value = new self($value);
            }

            $this->data->$key = $value;
        }
    }

    public function __get($key)
    {
        return $this->get($key);
    }

    public function get($key)
    {
        if (isset($this->data->$key)) {
            return $this->data->$key;
        }

        return null;
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
            return $this->get($key);
        }
        // Sets a value to a property and returns it'self e.g.: $object->setProperty('value') -> returns Object class
        if (strpos($alias, 'set') === 0 && !empty($key)) {
            $value = reset($args);

            if (is_array($value) || is_object($value)) {
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
            // If there is an argument setted, we check the value agains the argument e.g.: isRole('admin'), isEncoding('base64')
            if ($value) {
                return isset($this->data->$key) ? $this->data->$key === $value : false;
            }
            return isset($this->data->$key) ? (bool) $this->data->$key : false;
        }
        // If the called function is not a set/get/unset/is kind of thing,
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
    public function getIterator()
    {
        return new \ArrayIterator($this->data);
    }
    
    /**
     * Assign a value to the specified offset
     * @overrides \ArrayAccess::offsetSet
     * @param  mixed $offset The offset to assign the value to
     * @param  mixed $value  The value to set
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $offset = $this->count();
        }
        $this->data->$offset = $value;
    }
    
    /**
     * Checks if an offset exists
     * @overrides \ArrayAccess::offsetExists
     * @param  mixed $offset An offset to check for
     * @return boolean Returns TRUE on success or FALSE on failure
     */
    public function offsetExists($offset)
    {
        return isset($this->data->$offset);
    }
    
    /**
     * Retrives an offset value
     * @overrides \ArrayAccess::offsetGet
     * @param  mixed $offset The offset to retrieve
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Unsets an offset
     * @overrides \ArrayAccess::offsetUnset
     * @param  mixed $offset The offset to unset
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->data->$offset);
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

    /**
     * Returns the number of elements in the object
     * @return integer
     */
    public function count()
    {
        return count((array) $this->data);
    }

    /**
     * Recursively converts all the Object instances to array
     * @return array
     */
    public function toArray($convertRecursively = true)
    {
        if ($convertRecursively) {
            // We use the json serializer as help to recursively convert the Object instances
            return json_decode(json_encode($this), true);
        } else {
            return (array) $this->data;
        }
    }

    /**
     * Recursively converts all the Object instances to \stdClass instances
     * @return \stdClass
     */
    public function toObject($convertRecursively = true)
    {
        if ($convertRecursively) {
            // We use the json serializer as help to recursively convert the Object instances
            return json_decode(json_encode($this));
        } else {
            return $this->data;
        }
    }
}
