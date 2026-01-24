<?php

declare(strict_types=1);

namespace App\Models\Concerns;

trait HasAddressFormatting
{
    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function getFormattedAddressAttribute(): string
    {
        $parts = [
            $this->street,
            trim("{$this->postal_code} {$this->city}"),
            $this->country,
        ];

        return implode(', ', array_filter($parts));
    }

    public function getFullFormattedAddressAttribute(): string
    {
        $lines = [];

        if (!empty($this->company_name)) {
            $lines[] = $this->company_name;
        }

        $lines[] = $this->full_name;
        $lines[] = (string) $this->street;
        $lines[] = trim("{$this->postal_code} {$this->city}");
        $lines[] = (string) $this->country;

        if (!empty($this->phone)) {
            $lines[] = "Tel: {$this->phone}";
        }

        return implode("\n", array_filter($lines));
    }

    public function isCompany(): bool
    {
        return !empty($this->company_name) || !empty($this->ico);
    }
}
