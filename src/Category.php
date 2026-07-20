<?php

class Category {
    public int $id;
    public string $name;

    public function __construct(int $id, string $name) {
        $this->id = $id;
        $this->name = $name;
    }

    public function label(): string {
        return '[' . $this->id . '] ' . $this->name;
    }
}