<?php

namespace App\Modules\Catalog\DTOs;

class CreateCategoryDTO
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $description = null,
        public readonly ?int $parentId = null,
        public readonly bool $isActive = true,
    ) {
    }

    public static function fromRequest(\Illuminate\Http\Request $request): self
    {
        return new self(
            name: $request->input('name'),
            description: $request->input('description'),
            parentId: $request->input('parent_id'),
            isActive: $request->boolean('is_active', true),
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'parent_id' => $this->parentId,
            'is_active' => $this->isActive,
        ];
    }
}
