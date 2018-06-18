<?php
/*
 * This file is based on code from PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace LoversOfBehat\TableExtension;

use LoversOfBehat\TableExtension\Exception\NoArraySubsetException;

/**
 * Asserts that an array has a specified subset.
 *
 * This is based on the array subset constraint from PHPUnit.
 *
 * @see \PHPUnit\Framework\Constraint\ArraySubset
 */
class AssertArraySubset
{
    /**
     * @var array
     */
    private $subset;

    /**
     * @var bool
     */
    private $strict;

    public function __construct(array $subset, bool $strict = false)
    {
        $this->strict = $strict;
        $this->subset = $subset;
    }

    /**
     * Evaluates the constraint for parameter $other
     *
     * @param array $other
     *   The array to compare.
     *
     * @throws NoArraySubsetException
     *   Thrown when the subset is not part of the passed array.
     */
    public function evaluate(array $other): void
    {
        $intersect = $this->arrayIntersectRecursive($other, $this->subset);
        $this->deepSort($intersect);
        $this->deepSort($this->subset);

        $result = $this->compare($intersect, $this->subset);

        if (!$result) {
            throw new NoArraySubsetException();
        }
    }

    private function isAssociative(array $array): bool
    {
        return \array_reduce(\array_keys($array), function (bool $carry, $key): bool {
            return $carry || \is_string($key);
        }, false);
    }

    private function compare($first, $second): bool
    {
        return $this->strict ? $first === $second : $first == $second;
    }

    private function deepSort(array &$array): void
    {
        foreach ($array as &$value) {
            if (\is_array($value)) {
                $this->deepSort($value);
            }
        }

        if ($this->isAssociative($array)) {
            \ksort($array);
        } else {
            \sort($array);
        }
    }

    private function arrayIntersectRecursive(array $array, array $subset): array
    {
        $intersect = [];

        if ($this->isAssociative($subset)) {
            // If the subset is an associative array, get the intersection while
            // preserving the keys.
            foreach ($subset as $key => $subset_value) {
                if (\array_key_exists($key, $array)) {
                    $array_value = $array[$key];

                    if (\is_array($subset_value) && \is_array($array_value)) {
                        $intersect[$key] = $this->arrayIntersectRecursive($array_value, $subset_value);
                    } elseif ($this->compare($subset_value, $array_value)) {
                        $intersect[$key] = $array_value;
                    }
                }
            }
        } else {
            // If the subset is an indexed array, loop over all entries in the
            // haystack and check if they match the ones in the subset.
            foreach ($array as $array_key => $array_value) {
                if (\is_array($array_value)) {
                    foreach (array_diff_key($subset, $intersect) as $key => $subset_value) {
                        if (\is_array($subset_value)) {
                            $recursed = $this->arrayIntersectRecursive($array_value, $subset_value);

                            if (!empty($recursed)) {
                                $intersect[$key] = $recursed;

                                break;
                            }
                        }
                    }
                } else {
                    // Skip values that have already been matched.
                    foreach (array_diff_key($subset, $intersect) as $key => $subset_value) {
                        if (!\is_array($subset_value) && $this->compare($subset_value, $array_value)) {
                            $intersect[$key] = $array_value;

                            break;
                        }
                    }
                }
            }
        }

        return $intersect;
    }
}
