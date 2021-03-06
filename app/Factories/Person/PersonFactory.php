<?php

namespace App\Factories\Person;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PersonFactory implements PersonFactoryContract
{
    /**
     * @var Model
     */
    private $model;
    /**
     * @var array
     */
    private $parameters;

    public function __construct(Model $model, array $parameters)
    {
        $this->model = $model;
        $this->parameters = $parameters;
    }

    /**
     * @return Model
     * @throws ValidationException
     */
    public function create(): Model
    {
        $this->validate();

        $this->model = $this->model->create($this->parseParameters());

        $this->attachToMovie();

        return $this->model;
    }

    /**
     * @return Model
     * @throws ValidationException
     */
    public function update(): Model
    {
        $this->validate();

        $this->model = $this->model->update($this->parseParameters());

        $this->syncWithMovie();

        return $this->model;
    }

    public function attachToMovie(): void
    {
        if (isset($this->parameters['movie_ids'])) {
            $this->model->movies()->attach($this->parameters['movie_ids']);
        }
    }

    private function syncWithMovie(): void
    {
        if (isset($this->parameters['movie_ids'])) {
            $this->model->movies()->sync($this->parameters['movie_ids']);
        }
    }

    /**
     * @return bool
     * @throws ValidationException
     */
    public function validate(): bool
    {
        $validator = Validator::make($this->parameters, self::validationRules());

        if ($validator->fails()) {
            throw new ValidationException($validator, $validator->getMessageBag(), $validator->errors());
        }

        return true;
    }

    /**
     * @return array
     */
    private function parseParameters(): array
    {
        $fullName = Arr::get($this->parameters, 'full_name');
        $slug = Arr::get($this->parameters, 'slug');
        return [
            'full_name' => $fullName,
            'poster' => Arr::get($this->parameters, 'poster'),
            'slug' => $slug ?? Str::slug($fullName, '-'),
        ];
    }

    /**
     * @return array
     */
    private static function validationRules(): array
    {
        return [
            'full_name' => 'required|string|regex:/(?=^.{5,50}$)^[a-zA-Z-]+\s[a-zA-Z-]+$/',
            'poster' => 'nullable|string|url',
            'movie_ids' => 'array',
            'movie_ids.*' => 'nullable|integer|exists:movies,id'
        ];
    }
}
