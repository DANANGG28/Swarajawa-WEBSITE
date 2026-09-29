<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class UniqueUserEmail implements ValidationRule
{
    public function __construct(
        protected ?string $ignoreTable = null,
        protected mixed $ignoreId = null,
        protected ?string $customMessage = 'Email sudah digunakan oleh akun lain.',
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null || $value === '') {
            return;
        }

        $email = mb_strtolower(trim((string) $value));
        $tables = ['guru', 'siswa', 'superadmin'];

        foreach ($tables as $table) {
            $query = DB::table($table)->whereRaw('LOWER(TRIM(email)) = ?', [$email]);

            if ($this->ignoreTable === $table && $this->ignoreId !== null) {
                $query->where('id', '!=', $this->ignoreId);
            }

            if ($query->exists()) {
                $fail($this->customMessage ?? 'Email sudah digunakan oleh akun lain.');

                return;
            }
        }
    }
}
