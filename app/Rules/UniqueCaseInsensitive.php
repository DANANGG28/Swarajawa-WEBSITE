<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class UniqueCaseInsensitive implements ValidationRule
{
    /**
     * @param  array<string, mixed>  $where
     */
    public function __construct(
        protected string $table,
        protected string $column,
        protected mixed $ignoreId = null,
        protected ?string $ignoreColumn = 'id',
        protected array $where = [],
        protected ?string $customMessage = null,
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null || $value === '') {
            return;
        }

        $normalized = mb_strtolower(trim((string) $value));

        $query = DB::table($this->table)
            ->whereRaw('LOWER(TRIM('.$this->column.')) = ?', [$normalized]);

        if ($this->ignoreId !== null) {
            $query->where($this->ignoreColumn ?? 'id', '!=', $this->ignoreId);
        }

        foreach ($this->where as $col => $val) {
            if ($val === null) {
                $query->whereNull($col);
            } else {
                $query->where($col, $val);
            }
        }

        if ($query->exists()) {
            $fail($this->customMessage ?? ':attribute sudah digunakan.');
        }
    }
}
