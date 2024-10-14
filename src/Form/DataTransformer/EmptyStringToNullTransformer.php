<?php
namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class EmptyStringToNullTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        // Оставяме стойността непроменена при представяне
        return $value;
    }

    public function reverseTransform($value)
    {
        // Преобразуваме празния низ в null
        if ($value === '') {
            return null;
        }

        return $value;
    }
}