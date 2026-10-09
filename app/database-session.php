<?php
/** Shared MySQL session data and locks survive serverless instance/redeployment changes. */
final class DatabaseSession implements SessionHandlerInterface,SessionIdInterface,SessionUpdateTimestampHandlerInterface {
 private ?string $lock=null;
 private function key(string $id): string { return hash('sha256',$id); }
 public function open(string $path,string $name): bool {return true;}
 public function close(): bool {if($this->lock!==null){query('SELECT RELEASE_LOCK(?)',[$this->lock]);$this->lock=null;}return true;}
 public function read(string $id): string|false { $this->close();$this->lock='tdh-session:'.substr($this->key($id),0,48);if(!(int)query('SELECT GET_LOCK(?,10)',[$this->lock])->fetchColumn())throw new RuntimeException('Session is busy.');$r=one('SELECT data FROM sessions WHERE id_hash=? AND expires_at>?',[$this->key($id),time()]);return $r?(string)$r['data']:''; }
 public function write(string $id,string $data): bool {query('INSERT INTO sessions(id_hash,data,expires_at) VALUES(?,?,?) ON DUPLICATE KEY UPDATE data=VALUES(data),expires_at=VALUES(expires_at)',[$this->key($id),$data,time()+7200]);return true;}
 public function destroy(string $id): bool {query('DELETE FROM sessions WHERE id_hash=?',[$this->key($id)]);return true;}
 public function gc(int $max_lifetime): int|false {return query('DELETE FROM sessions WHERE expires_at<?',[time()])->rowCount();}
 public function create_sid(): string {return bin2hex(random_bytes(32));}
 public function validateId(string $id): bool {return preg_match('/^[a-f0-9]{64}$/D',$id)===1&&one('SELECT id_hash FROM sessions WHERE id_hash=? AND expires_at>?',[$this->key($id),time()])!==null;}
 public function updateTimestamp(string $id,string $data): bool {return $this->write($id,$data);}
}
