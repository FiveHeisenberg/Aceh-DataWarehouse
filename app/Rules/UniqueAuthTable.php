<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class UniqueAuthTable implements ValidationRule
{
    public function __construct(
        private readonly string $table,
        private readonly string $column,
        private readonly string $message = '',
        private readonly string $connection = 'db_auth',
        private readonly mixed $ignoreKey = null,
        private readonly string $keyColumn = 'id_user',
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null || $value === '') {
            return;
        }

        $query = DB::connection($this->connection)
            ->table($this->table)
            ->where($this->column, $value);

        if ($this->ignoreKey !== null) {
            $query->where($this->keyColumn, '!=', $this->ignoreKey);
        }

        if ($query->exists()) {
            $fail($this->message !== '' ? $this->message : "{$this->column} sudah digunakan.");
        }
    }
}
