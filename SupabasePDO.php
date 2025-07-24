<?php

class SupabasePDO
{
  private $apiUrl;
  private $apiKey;
  public $lastQuery;

  public function __construct($apiUrl, $apiKey)
  {
    $this->apiUrl = rtrim($apiUrl, '/');
    $this->apiKey = $apiKey;
  }

  public function query($sql)
  {
    $this->lastQuery = $sql;

    if (stripos($sql, 'SELECT') === 0) {
      if (preg_match('/SELECT\s+(.+)\s+FROM\s+(\w+)/i', $sql, $matches)) {
        $columnsRaw = $matches[1];
        $table = $matches[2];

        $columns = array_map('trim', explode(',', $columnsRaw));
        $selectParts = [];

        foreach ($columns as $col) {
          if (preg_match('/(\w+)\s+AS\s+(\w+)/i', $col, $aliasMatch)) {
            $selectParts[] = "{$aliasMatch[1]}:{$aliasMatch[2]}";
          } else {
            $selectParts[] = $col;
          }
        }

        $selectQuery = implode(",", $selectParts);
        $url = "{$this->apiUrl}/rest/v1/{$table}?select={$selectQuery}";

        $res = $this->send("GET", $url);
        return new SupabasePDOStatement($res);
      }
    }

    throw new Exception("Unsupported query: $sql");
  }

  public function prepare($sql)
  {
    $this->lastQuery = $sql;
    return new SupabasePDOPreparedStatement($this, $sql);
  }

  public function execute(array $params = [])
  {
    $sql = $this->lastQuery;
    $normalizedSql = preg_replace('/\s+/', ' ', trim($sql));

    // SELECT * FROM ... WHERE ...
    if (stripos($normalizedSql, "SELECT * FROM") === 0) {
      if (preg_match('/SELECT \* FROM (\w+) WHERE (\w+) = \?/i', $normalizedSql, $matches)) {
        $table = $matches[1];
        $column = $matches[2];
        $value = $params[0];

        $url = "{$this->apiUrl}/rest/v1/{$table}?{$column}=eq.{$value}";
        $res = $this->send("GET", $url);
        return new SupabasePDOStatement($res);
      }
    }

    // SELECT ... FROM ... WHERE ...
    if (stripos($normalizedSql, "SELECT") === 0) {
      if (preg_match('/SELECT (.+?) FROM (\w+) WHERE (\w+) = \?/i', $normalizedSql, $matches)) {
        $columnsRaw = $matches[1];
        $table = $matches[2];
        $whereCol = $matches[3];
        $value = $params[0];

        $columns = array_map('trim', explode(',', $columnsRaw));
        $selectParts = [];

        foreach ($columns as $col) {
          if (preg_match('/(\w+)\s+AS\s+(\w+)/i', $col, $aliasMatch)) {
            $selectParts[] = "{$aliasMatch[1]}:{$aliasMatch[2]}";
          } else {
            $selectParts[] = $col;
          }
        }

        $selectQuery = implode(",", $selectParts);
        $url = "{$this->apiUrl}/rest/v1/{$table}?select={$selectQuery}&{$whereCol}=eq.{$value}";
        $res = $this->send("GET", $url);
        return new SupabasePDOStatement($res);
      }
    }

    // UPDATE ... SET ... WHERE ...
    if (stripos($normalizedSql, "UPDATE") === 0) {
      if (preg_match('/UPDATE (\w+) SET (.+?) WHERE (\w+) = \?/i', $normalizedSql, $matches)) {
        $table = $matches[1];
        $setPart = $matches[2];
        $whereCol = $matches[3];

        preg_match_all('/(\w+)\s*=\s*\?/i', $setPart, $setMatches);
        $columns = $setMatches[1];
        $data = array_combine($columns, array_slice($params, 0, count($columns)));
        $idValue = $params[count($columns)];

        $url = "{$this->apiUrl}/rest/v1/{$table}?{$whereCol}=eq.{$idValue}";
        $this->send("PATCH", $url, $data);
        return true;
      }
    }

    // DELETE FROM ... WHERE ...
    if (stripos($normalizedSql, "DELETE FROM") === 0) {
      if (preg_match('/DELETE FROM (\w+) WHERE (\w+) = \?/i', $normalizedSql, $matches)) {
        $table = $matches[1];
        $column = $matches[2];
        $value = $params[0];

        $url = "{$this->apiUrl}/rest/v1/{$table}?{$column}=eq.{$value}";
        $this->send("DELETE", $url);
        return true;
      }
    }

    // INSERT INTO ... (...) VALUES (...)
    if (stripos($normalizedSql, "INSERT INTO") === 0) {
      if (preg_match('/INSERT INTO (\w+)\s*\(([^)]+?)\)\s*VALUES\s*\(([^)]+?)\)/i', $normalizedSql, $matches)) {
        $table = trim($matches[1]);
        $columns = array_map('trim', explode(',', $matches[2]));
        $placeholders = array_map('trim', explode(',', $matches[3]));

        if (count($columns) !== count($placeholders)) {
          throw new Exception("Column and value count mismatch in INSERT.");
        }

        $data = [];
        $paramIndex = 0;

        foreach ($placeholders as $i => $placeholder) {
          $col = $columns[$i];

          if ($placeholder === '?') {
            $data[$col] = $params[$paramIndex++];
          } elseif (preg_match("/^'(.*)'$/", $placeholder, $m)) {
            $data[$col] = $m[1];
          } elseif (is_numeric($placeholder)) {
            $data[$col] = $placeholder + 0;
          } elseif (strtolower($placeholder) === 'null') {
            $data[$col] = null;
          } else {
            throw new Exception("Unsupported literal in INSERT: $placeholder");
          }
        }

        unset($data['booking_id']);

        $url = "{$this->apiUrl}/rest/v1/{$table}";
        $this->send("POST", $url, $data);
        return true;
      }
    }

    throw new Exception("Unsupported prepared statement: $normalizedSql");
  }

  private function send($method, $url, $data = null)
  {
    $headers = [
      "apikey: {$this->apiKey}",
      "Authorization: Bearer {$this->apiKey}",
      "Content-Type: application/json",
      "Prefer: return=representation"
    ];

    $options = [
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_CUSTOMREQUEST => $method,
      CURLOPT_HTTPHEADER => $headers,
    ];

    if ($data !== null) {
      $options[CURLOPT_POSTFIELDS] = json_encode($data);
    }

    $ch = curl_init();
    curl_setopt_array($ch, $options);
    $result = curl_exec($ch);

    if (curl_errno($ch)) {
      throw new Exception('Curl error: ' . curl_error($ch));
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode >= 400) {
      throw new Exception("HTTP $httpCode: $result");
    }

    return json_decode($result, true);
  }
}

class SupabasePDOPreparedStatement
{
  private $pdo;
  private $sql;
  private $result;

  public function __construct($pdo, $sql)
  {
    $this->pdo = $pdo;
    $this->sql = $sql;
  }

  public function execute(array $params = [])
  {
    $this->pdo->lastQuery = $this->sql;
    $result = $this->pdo->execute($params);

    if ($result instanceof SupabasePDOStatement) {
      $this->result = $result;
    } else {
      $this->result = null;
    }

    return true;
  }

  public function fetch()
  {
    return $this->result ? $this->result->fetch() : false;
  }

  public function fetchAll()
  {
    return $this->result ? $this->result->fetchAll() : [];
  }

  public function fetchColumn($index = 0)
  {
    return $this->result ? $this->result->fetchColumn($index) : false;
  }
}

class SupabasePDOStatement
{
  private $data;

  public function __construct($data)
  {
    $this->data = $data;
  }

  public function fetchAll($mode = PDO::FETCH_ASSOC)
  {
    return $this->data;
  }

  public function fetch($mode = PDO::FETCH_ASSOC)
  {
    return $this->data[0] ?? false;
  }

  public function fetchColumn($columnIndex = 0)
  {
    $row = $this->fetch();
    if (is_array($row)) {
      return array_values($row)[$columnIndex] ?? false;
    }
    return false;
  }
}
