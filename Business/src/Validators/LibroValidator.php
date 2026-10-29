<?php

declare(strict_types=1);

namespace App\Validators;

use Respect\Validation\Validator;
use Respect\Validation\Exceptions\ValidationException;

final class LibroValidator
{
    public function validate(array $data): array
    {
        $validator = Validator::key(
                'isbn',
                Validator::stringType()
                    ->notEmpty()
                    ->length(1, 30)
                    ->regex('/^[0-9]{1,5}(-[0-9]{1,7}){2,3}-[0-9X]$/')
            )
            ->key('titulo', Validator::stringType()->notEmpty()->length(1, 150))
            ->key('autor', Validator::stringType()->notEmpty()->length(1, 100))
            ->key('anio_publicacion', Validator::intType()->between(1000, (int) date('Y')))
            ->key('ejemplares_disponibles', Validator::intType()->min(0));

        try {
            $validator->assert($data);
        } catch (ValidationException $exception) {
            return $exception->getMessages();
        }

        return [];
    }
}
