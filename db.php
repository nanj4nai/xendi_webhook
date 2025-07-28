<?php
require_once "SupabasePDO.php"; // Custom class you'll need to implement

$apiUrl = "https://hcrtnwdvfcrjftahzybm.supabase.co";
$apiKey = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImhjcnRud2R2ZmNyamZ0YWh6eWJtIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NTE4NTM1NzEsImV4cCI6MjA2NzQyOTU3MX0.SKAL-qnyTFGiwqO9-7-pFeuzw9TQXcYtfM49gB6Ae8I";

try {
  $pdo = new SupabasePDO($apiUrl, $apiKey); // You must define this class
} catch (Exception $e) {
  die("Database connection failed: " . $e->getMessage());
}
?>
