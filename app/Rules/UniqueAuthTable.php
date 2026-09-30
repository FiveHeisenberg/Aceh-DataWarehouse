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
    ) {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null || $value === '') {
            return;
        }

        $exists = DB::connection($this->connection)
            ->table($this->table)
            ->where($this->column, $value)
            ->exists();

        if ($exists) {
            $fail($this->message !== '' ? $this->message : "{$this->column} sudah digunakan.");
        }
    }
}
