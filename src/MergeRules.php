<?php

namespace Pyxeel\MergeRules;

use Exception;

class MergeRules
{
    protected $rule = 0,
        $prefix = 1,
        $required = 2,
        $countToBePrefix = 3,
        $request = [],
        $current,
        $args;

    public function merge(...$args)
    {
        $this->args = $args;

        try {
            foreach ($this->args as $array) {

                $this->setRules($array);

                if (!$this->validate())
                    $this->invalidType();

                if ($this->hasException())
                    continue;

                if ($this->needPrefix()) {
                    $prefix = $this->prefix();

                    $argsPrefix = [
                        $prefix => ($this->isRequired() ? '' : 'required|') . 'array'
                    ];

                    $argsPrefixItems = $this->getPrefixed($prefix);

                    $this->mergeRequest($argsPrefix, $argsPrefixItems);
                } else {
                    $rule = $this->current();

                    $this->mergeRequest($rule);
                }
            }

            return $this->request;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Return the current rule
     *
     * @return array
     */
    private function current(): array
    {
        return $this->current;
    }

    /**
     * Merge the diferents rules
     *
     * @param array ...$arrays
     * @return void
     */
    private function mergeRequest(...$arrays): void
    {
        foreach ($arrays as $array) {
            $this->request = array_merge($this->request, $array);
        }
    }

    /**
     * Return prefix
     *
     * @return string
     */
    private function prefix(): string
    {
        return $this->current[$this->prefix];
    }

    /**
     * Set rules in current property
     *
     * @param array $array
     * @return void
     */
    private function setRules($array): void
    {
        $this->current = $array;
    }

    /**
     * Get keys from the rules
     *
     * @param array $array
     * @return array
     */
    private function getKeys($array): array
    {
        return array_keys($array);
    }

    /**
     * Dispatch a error of invalid type
     *
     * @return void
     */
    private function invalidType()
    {
        $message = "All parameters must be array type.";

        throw new Exception($message);
    }

    /**
     * Validate input data
     *
     * @return boolean
     */
    private function validate(): bool
    {
        return is_array($this->current);
    }

    /**
     * Return true if an array has an exception and not need treatment
     *
     * @return boolean
     */
    private function hasException(): bool
    {
        return empty($this->current);
    }

    /**
     * Return true if this parameter is an array witch contains a prefix to be placed
     *
     * @return boolean
     */
    private function needPrefix(): bool
    {
        $keys = $this->getKeys($this->current);

        return count($keys) <= $this->countToBePrefix
            && is_integer($keys[$this->rule])
            && is_integer($keys[$this->prefix]);
    }

    /**
     * Return if an array is required or not
     *
     * @return boolean
     */
    private function isRequired(): bool
    {
        return isset($this->current[$this->required]) && $this->current[$this->required] === true;
    }

    /**
     * Return an array with prefixes concatenated
     *
     * @param string $prefix
     * @return array
     */
    private function getPrefixed(string $prefix): array
    {
        return collect($this->current[$this->rule])->mapWithKeys(function ($el, $key) use ($prefix) {
            return [$prefix . '.' . $key => $el];
        })->toArray();
    }
}
