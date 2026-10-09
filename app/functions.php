<?php
class HttpError extends RuntimeException {}
function e($value): string { return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function db(): PDO {
 static $pdo;
 if (!$pdo) { $c = $GLOBALS['config']; $pdo = new PDO($c['db_dsn'], $c['db_user'], $c['db_password'], [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES=>false]); }
 return $pdo;
}
function query(string $sql, array $args=[]): PDOStatement { $s=db()->prepare($sql); $s->execute($args); return $s; }
function rows(string $sql,array $args=[]): array { return query($sql,$args)->fetchAll(); }
function one(string $sql,array $args=[]): ?array { return query($sql,$args)->fetch() ?: null; }
function setting(string $key,string $fallback=''): string { static $all; if($all===null){$all=array_column(rows('SELECT * FROM settings'),'setting_value','setting_key');} return $all[$key]??$fallback; }
function url(string $route=''): string { return rtrim($GLOBALS['config']['base_url'],'/').(($GLOBALS['config']['clean_urls']??false)?'/'.ltrim($route,'/'):'/index.php'.($route!==''?'?route='.rawurlencode($route):'')); }
function asset(string $path): string { return rtrim($GLOBALS['config']['base_url'],'/').'/'.$path; }
function redirect(string $route): never { header('Location: '.url($route),true,303); exit; }
function csrf(): string { return $_SESSION['csrf']??=bin2hex(random_bytes(32)); }
function csrf_field(): string { return '<input type="hidden" name="csrf" value="'.e(csrf()).'">'; }
function check_csrf(): void { if(!hash_equals(csrf(),(string)($_POST['csrf']??''))){http_response_code(403);throw new HttpError('Request expired. Please reload and try again.',403);} }
function flash(string $text): void { $_SESSION['flash']=$text; }
function text_block(string $value): string { return '<div class="prose">'.nl2br(e($value)).'</div>'; }
function phone_valid(string $number): bool { return preg_match('/^[1-9][0-9]{7,14}$/D',$number)===1; }
function whatsapp(string $message): ?string { $n=setting('whatsapp_number'); return phone_valid($n)?'https://wa.me/'.$n.'?text='.rawurlencode($message):null; }
function duration(array $p): string { return $p['duration_days'].' '.((int)$p['duration_days']===1?'day':'days'); }
function package_message(array $p,array $details=[]): string {
 $lines=['Hello The Dream Holidays!','I would like to book this package.','','Package: '.$p['name'],'Duration: '.duration($p)];
 foreach(['name'=>'Name','departure_city'=>'Departure City','travel_date'=>'Travel Date','adults'=>'Adults','children'=>'Children','message'=>'Message'] as $key=>$label){if(isset($details[$key]) && trim((string)$details[$key])!=='')$lines[]=$label.': '.$details[$key];}
 $lines[]='Package Link: '.url('package/'.$p['slug']);$lines[]='';$lines[]='Please confirm availability and share the quotation.';return implode("\n",$lines);
}
function contact_alternative(): string { $p=setting('phone');$mail=setting('email'); if(preg_match('/^\+?[0-9 ()-]{8,25}$/D',$p))return '<span class="help">Booking chat is unavailable. <a href="tel:'.e(preg_replace('/[^+0-9]/','',$p)).'">Call our team</a>.</span>';if(filter_var($mail,FILTER_VALIDATE_EMAIL))return '<span class="help">Booking chat is unavailable. <a href="mailto:'.e($mail).'">Email our team</a>.</span>';return '<span class="help">Booking contact details are awaiting business approval. Please use our <a href="'.e(url('contact')).'">website enquiry form</a>.</span>'; }
function booking_button(string $message): string { $u=whatsapp($message);return $u?'<a class="button booking" href="'.e($u).'" target="_blank" rel="noopener noreferrer">Booking</a>':'<button class="button booking" disabled>Booking</button>'.contact_alternative(); }
function media_for(string $type,int $id): array { return rows('SELECT * FROM media WHERE owner_type=? AND owner_id=? AND status=? ORDER BY sort_order,id',[$type,$id,'published']); }
function picture(?array $m,string $class=''): string { if(!$m)return '<div class="image-placeholder '.e($class).'" role="img" aria-label="Photograph awaiting approval"><span>Photograph awaiting approval</span></div>';return '<img class="'.e($class).'" src="'.e(asset('media/'.$m['filename'])).'" alt="'.e($m['alt_text']).'" width="1200" height="800" loading="lazy">'; }
function rate_limit(string $scope,int $limit,int $seconds): bool {
 $bucket=hash('sha256',$scope.'|'.($_SERVER['REMOTE_ADDR']??'local'));$now=time();
 query('INSERT INTO rate_limits(bucket,attempts,expires_at) VALUES(?,1,?) ON DUPLICATE KEY UPDATE attempts=IF(expires_at<=?,1,attempts+1),expires_at=IF(expires_at<=?,VALUES(expires_at),expires_at)',[$bucket,$now+$seconds,$now,$now]);
 if(random_int(1,50)===1)query('DELETE FROM rate_limits WHERE expires_at<?',[$now]);
 return (int)one('SELECT attempts FROM rate_limits WHERE bucket=?',[$bucket])['attempts']<=$limit;
}
function require_admin(): void { if(empty($_SESSION['admin_id']))redirect('admin/login');if(time()-($_SESSION['last_active']??0)>1800){unset($_SESSION['admin_id']);flash('Session expired. Please sign in.');redirect('admin/login');}$_SESSION['last_active']=time(); }
function validated_travel(array $input,bool $enquiry): array {
 $errors=[];$result=[];$required=$enquiry?['full_name','mobile','interest','departure_city','travel_date','adults']:['name','departure_city','travel_date','adults'];
 foreach($required as $key){$v=trim((string)($input[$key]??''));if($v==='')$errors[]='Please provide '.str_replace('_',' ',$key).'.';$result[$key]=$v;}
 foreach($enquiry?['email','vehicle_preference','message']:['message'] as $key)$result[$key]=trim((string)($input[$key]??''));
 $result['children']=trim((string)($input['children']??'0'));
 foreach($result as $key=>$v){if(strlen($v)>($key==='message'?4000:190))$errors[]='The '.str_replace('_',' ',$key).' field is too long.';}
 if($enquiry && !preg_match('/^\+?[0-9 ()-]{8,25}$/D',$result['mobile']))$errors[]='Provide a valid mobile number.';
 if($enquiry && $result['email']!==''&&!filter_var($result['email'],FILTER_VALIDATE_EMAIL))$errors[]='Provide a valid email address.';
 $date=DateTimeImmutable::createFromFormat('!Y-m-d',$result['travel_date']);if(!$date||$date->format('Y-m-d')!==$result['travel_date']||$result['travel_date']<date('Y-m-d'))$errors[]='Choose today or a future travel date.';
 foreach(['adults'=>1,'children'=>0] as $key=>$min){if(filter_var($result[$key],FILTER_VALIDATE_INT,['options'=>['min_range'=>$min,'max_range'=>500]])===false)$errors[]='Provide a valid '.$key.' count ('.$min.'–500).';}
 return [$result,$errors];
}

function public_dir(): string { return $GLOBALS['config']['public_dir']??dirname(__DIR__).'/public'; }

function brand_markup(): string {
 $file=setting('logo_filename');
 if(preg_match('/^[a-f0-9]{40}\.webp$/D',$file)&&is_file(public_dir().'/media/'.$file))
  return '<img class="business-logo" src="'.e(asset('media/'.$file)).'" alt="The Dream Holidays — Travel / Transportation" width="190" height="76">';
 foreach(['business-logo.png','business-logo.webp'] as $logo)if(is_file(public_dir().'/assets/'.$logo))
  return '<img class="business-logo" src="'.e(asset('assets/'.$logo)).'" alt="The Dream Holidays — Travel / Transportation" width="190" height="76">';
 return '<img src="'.e(asset('assets/logo.svg')).'" alt="" width="44" height="44"><span>The Dream <strong>Holidays</strong><small>Travel · Explore · Connect</small></span>';
}
