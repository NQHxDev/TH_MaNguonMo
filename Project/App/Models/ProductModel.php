<?php

class ProductModel {

   private int $ID;

   private string $Name;

   private string $Description;

   private float $Price;

   public function __construct(int $id, string $name, string $description, float $price) {
      $this->ID = $id;
      $this->Name = $name;
      $this->Description = $description;
      $this->Price = $price;
   }

   public function getID() {
      return $this->ID;
   }

   public function getName() {
      return $this->Name;
   }

   public function getDescription() {
      return $this->Description;
   }

   public function getPrice() {
      return $this->Price;
   }

   public function setID(int $id) {
      $this->ID = $id;
   }

   public function setName(string $name) {
      $this->Name = $name;
   }

   public function setDescription(string $description) {
      $this->Description = $description;
   }

   public function setPrice(float $price) {
      $this->Price = $price;
   }

}

?>
