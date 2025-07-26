<?php
require_once "SupabasePDO.php"; // Custom class you'll need to implement

$apiUrl = "https://fwyorzhxakxbdawkvmta.supabase.co";
$apiKey = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImZ3eW9yemh4YWt4YmRhd2t2bXRhIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NTM1MjY2ODUsImV4cCI6MjA2OTEwMjY4NX0.uThuifm7Wbf5ogff-jVj52SWVmydp5dQTHimDk_L354";

try {
  $pdo = new SupabasePDO($apiUrl, $apiKey); // You must define this class
} catch (Exception $e) {
  die("Database connection failed: " . $e->getMessage());
}
?>
