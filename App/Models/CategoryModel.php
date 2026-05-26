<?php

class CategoryModel {
    private int $id;
    private string $name;
    private ?string $description;

    public function __construct(int $id, string $name, ?string $description = null) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
    }

    public function getId(): int {
        return $this->id;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    public static function getAll(PDO $db): array {
        $stmt = $db->query("SELECT * FROM category ORDER BY id ASC");
        $categories = [];
        while ($row = $stmt->fetch()) {
            $categories[] = new CategoryModel((int)$row['id'], $row['name'], $row['description']);
        }
        return $categories;
    }

    public static function getById(PDO $db, int $id): ?CategoryModel {
        $stmt = $db->prepare("SELECT * FROM category WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row) {
            return new CategoryModel((int)$row['id'], $row['name'], $row['description']);
        }
        return null;
    }
}
?>
