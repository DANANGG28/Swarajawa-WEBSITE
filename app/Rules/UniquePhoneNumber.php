<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class UniquePhoneNumber implements ValidationRule
{
    public function __construct(
        protected ?string $ignoreTable = null,
        protected mixed $ignoreId = null,
        protected ?string $customMessage = 'Nomor telepon sudah digunakan oleh akun lain.',
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null || $value === '') {
            return;
        }

        $phone = preg_replace('/\D+/', '', (string) $value);
        if ($phone === '') {
            return;
        }

        $tables = ['guru', 'siswa', 'superadmin'];

        foreach ($tables as $table) {
            $query = DB::table($table)->where('no_telpon', $phone);

            if ($this->ignoreTable === $table && $this->ignoreId !== null) {
                $query->where('id', '!=', $this->ignoreId);
            }

            if ($query->exists()) {
                $fail($this->customMessage ?? 'Nomor telepon sudah digunakan oleh akun lain.');

                return;
            }
        }
    }
}
