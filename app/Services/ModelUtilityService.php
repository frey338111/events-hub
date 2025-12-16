<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use ReflectionClass;

class ModelUtilityService
{
    public function getEditableFields(string $modelClass): array
    {
        $reflection = new ReflectionClass($modelClass);
        /** @var Model $model */
        $model = $reflection->newInstance();
        $table = $model->getTable();
        $columns = Schema::getColumnListing($table);

        $fillable = $model->getFillable();
        $fields = [];
        foreach ($columns as $column) {
            if ($column === 'id' || ! in_array($column, $fillable, true)) {
                continue;
            }
            $fields[$column] = Schema::getColumnType($table, $column);
        }

        return $fields;
    }

    public function validatePayload(Request $request): bool
    {
        $modelName = $request->input('model_name');

        if (empty($modelName)) {
            return false;
        }

        $class = ltrim($modelName, '\\');
        if (! str_starts_with($class, 'App\\')) {
            $class = 'App\\Models\\'.$class;
        }

        if (! class_exists($class)) {
            return false;
        }

        $modelInstance = new $class;
        if (! $modelInstance instanceof Model) {
            return false;
        }

        $payload = $request->except(['_token', 'id', 'model_name']);

        $fillable = $modelInstance->getFillable();
        $invalidFields = array_diff(array_keys($payload), $fillable);

        if (! empty($invalidFields)) {
            return false;
        }

        return true;
    }

    public function updateModel(Request $request): array
    {
        if (! $this->validatePayload($request)) {
            return ['error' => true, 'errorMsg' => 'Request data is invalid'];
        }

        $modelName = $request->input('model_name');
        $payload = $request->except(['_token', 'id', 'model_name']);

        $id = $request->input('id');
        $class = 'App\\Models\\'.$modelName;

        if ($id !== null && $id !== '') {
            if (! ctype_digit((string) $id)) {
                return ['error' => true, 'errorMsg' => 'Invalid id'];
            }

            $existing = $class::find((int) $id);
            if (! $existing) {
                return ['error' => true, 'errorMsg' => 'Required entry not found'];
            }

            $existing->fill($payload);
            $existing->save();

            return ['error' => false];
        }

        $class::create($payload);

        return ['error' => false];
    }
}
