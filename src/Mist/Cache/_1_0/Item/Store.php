<?php

namespace Mist\Cache\_1_0\Item;

trait Store
{
    use Validation;

    protected $store;

    protected $isHydrated;

    protected $isHit;

    protected $key;

    protected $value;

    public function __construct($store, $key)
    {
        if ($this->assertCacheKeyBase($key) === false) {
            throw new InvalidArgumentException(
                'Passed key not valid',
                Exception::CACHE_1_0_INVALID_KEY
            );
        }

        $this->key        = $key;
        $this->store      = $store;
        $this->isHydrated = false;
    }

    protected abstract function internalGet($key, &$success);
    protected abstract function internalSet($key, $value, $ttl);
    protected abstract function internalDelete($key);
    protected abstract function internalExists($key);

    protected function hydrate()
    {
        if ($this->isHydrated === false) {
            $success     = false;
            $this->isHit = false;

            $this->value = $this->internalGet($this->key, $success);
            if ($success) {
                $this->isHit = true;
            }
            $this->isHydrated = true;
        }
    }

    /**
     * Returns the key for the current cache item.
     *
     * The key is loaded by the Implementing Library, but should be available to
     * the higher level callers when needed.
     *
     * @return string
     *   The key string for this cache item.
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Retrieves the value of the item from the cache associated with this objects key.
     *
     * The value returned must be identical to the value original stored by set().
     *
     * if isHit() returns false, this method MUST return null. Note that null
     * is a legitimate cached value, so the isHit() method SHOULD be used to
     * differentiate between "null value was found" and "no value was found."
     *
     * @return mixed
     *   The value corresponding to this cache item's key, or null if not found.
     */
    public function get()
    {
        $this->hydrate();
        return $this->value;
    }

    /**
     * Stores a value into the cache.
     *
     * The $value argument may be any item that can be serialized by PHP,
     * although the method of serialization is left up to the Implementing
     * Library.
     *
     * Implementing Libraries MAY provide a default TTL if one is not specified.
     * If no TTL is specified and no default TTL has been set, the TTL MUST
     * be set to the maximum possible duration of the underlying storage
     * mechanism, or permanent if possible.
     *
     * @param mixed $value
     *   The serializable value to be stored.
     * @param int|DateTime $ttl
     *   - If an integer is passed, it is interpreted as the number of seconds
     *     after which the item MUST be considered expired.
     *   - If a DateTime object is passed, it is interpreted as the point in
     *     time after which the the item MUST be considered expired.
     *   - If no value is passed, a default value MAY be used. If none is set,
     *     the value should be stored permanently or for as long as the
     *     implementation allows.
     * @return bool
     *   Returns true if the item was successfully saved, or false if there was
     *   an error.
     */
    public function set($value = null, $ttl = null)
    {
         // Validate value
        $a[] = assert(
            'is_string($value)
            || is_integer($value)
            || is_float($value)
            || is_bool($value)
            || is_null($value)
            || is_array($value)
            || is_object($value)',
            'Value is of an unsupported type'
        );

        $a[] = assert(
            '$value == unserialize(serialize($value))',
            'Value must lossless serialization and deserialization'
        );

        // Validate TTL
        $a[] = assert(
            'is_null($ttl) || is_integer($ttl) || $ttl instanceof \DateTime',
            'TTL is null, integer or \DateTime'
        );

        if (in_array(false, $a)) {
            return false;
        }
        // Validation done

        // TTL
        if ($ttl instanceof \DateTime) {
            $ttl = $ttl->format('U') - time();
        }
        // TTL Done

        $success = $this->internalSet($this->key, $value, $ttl);

        if ($success) {
            $this->isHit      = true;
            $this->value      = $value;
            $this->isHydrated = true;
        }

        return $success;
    }

    /**
     * Removes the current key from the cache.
     *
     * @return \Psr\Cache\CacheInterface
     *   The current item.
     */
    public function delete()
    {
        $success = $this->internalDelete($this->key);
        if ($success && $this->isHydrated) {
            $this->isHit = false;
            $this->value = null;
        }
        return $this;
    }

    /**
     * Confirms if the cache item lookup resulted in a cache hit.
     *
     * Note: This method MUST NOT have a race condition between calling isHit()
     * and calling get().
     *
     * @return bool
     *   True if the request resulted in a cache hit.  False otherwise.
     */
    public function isHit()
    {
        $this->hydrate();
        return $this->isHit;
    }

    /**
     * Confirms if the cache item exists in the cache.
     *
     * Note: This method MAY avoid retrieving the cached value for performance
     * reasons, which could result in a race condition between exists() and get().
     *
     * @return bool
     *  True if item exists in the cache, false otherwise.
     */
    public function exists()
    {
        return $this->internalExists($this->key);
    }
}
