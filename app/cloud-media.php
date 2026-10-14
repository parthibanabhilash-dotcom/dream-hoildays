<?php
function cloud_media_ready(): bool { $c=$GLOBALS['config'];return preg_match('/^[a-zA-Z0-9_-]+$/D',$c['cloudinary_cloud_name']??'')===1&&($c['cloudinary_api_key']??'')!==''&&($c['cloudinary_api_secret']??'')!==''; }
function upload_limit_mb(): int {return !empty($GLOBALS['config']['serverless'])?3:8;}
function media_url(string $filename): string {
 if(!preg_match('/^[a-f0-9]{40}\.webp$/D',$filename))return '';
 if(($GLOBALS['config']['media_driver']??'local')==='cloudinary')return cloud_media_ready()?'https://res.cloudinary.com/'.$GLOBALS['config']['cloudinary_cloud_name'].'/image/upload/dream-holidays/'.substr($filename,0,-5).'.webp':'';
 return asset('media/'.$filename);
}
function cloud_media_call(string $action,array $params,?string $path=null): array {
 if(!cloud_media_ready())throw new InvalidArgumentException('Image storage is not configured. Add the Cloudinary environment variables before uploading photos.');
 $c=$GLOBALS['config'];$params['timestamp']=(string)time();ksort($params);$pairs=[];foreach($params as $key=>$value)$pairs[]=$key.'='.$value;
 $params['signature']=sha1(implode('&',$pairs).$c['cloudinary_api_secret']);$params['api_key']=$c['cloudinary_api_key'];if($path)$params['file']=new CURLFile($path);
 $curl=curl_init('https://api.cloudinary.com/v1_1/'.$c['cloudinary_cloud_name'].'/image/'.$action);curl_setopt_array($curl,[CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>$params,CURLOPT_RETURNTRANSFER=>true,CURLOPT_CONNECTTIMEOUT=>10,CURLOPT_TIMEOUT=>25]);$raw=curl_exec($curl);$status=curl_getinfo($curl,CURLINFO_RESPONSE_CODE);$curlCode=curl_errno($curl);curl_close($curl);$result=is_string($raw)?json_decode($raw,true):null;
 if($status<200||$status>=300||!is_array($result)){
  // Never log response bodies: provider errors can contain signatures or account details.
  $reason='provider-error';$message='Image storage rejected the request. Check the Cloudinary account and upload settings.';
  $providerMessage=strtolower((string)($result['error']['message']??''));
  if($curlCode){$reason='network';$message='Could not connect to image storage. Please try again shortly.';}
  elseif($status===401||$status===403||str_contains($providerMessage,'signature')||str_contains($providerMessage,'api_key')){$reason='credentials';$message='Image storage authentication failed. Check that the Cloudinary cloud name, API key and API secret belong to the same environment, then redeploy.';}
  elseif($status===404||str_contains($providerMessage,'cloud name')){$reason='cloud-name';$message='Image storage account was not found. Check CLOUDINARY_CLOUD_NAME and redeploy.';}
  elseif($status===429||str_contains($providerMessage,'quota')||str_contains($providerMessage,'limit exceeded')){$reason='limit';$message='Image storage usage limit reached. Check the Cloudinary dashboard before trying again.';}
  error_log('Cloudinary operation failed: HTTP '.$status.', cURL '.$curlCode.', reason '.$reason.'.');
  throw new InvalidArgumentException($message);
 }return $result;
}
function cloud_media_upload(string $path): string {
 $id=bin2hex(random_bytes(20));$result=cloud_media_call('upload',['public_id'=>'dream-holidays/'.$id,'overwrite'=>'false','format'=>'webp','transformation'=>'c_limit,h_1600,w_1600'],$path);
 if(($result['public_id']??'')!=='dream-holidays/'.$id||($result['format']??'')!=='webp'||($result['width']??0)<1||($result['height']??0)<1||$result['width']>1600||$result['height']>1600)throw new RuntimeException('Image processing response was invalid.');return $id.'.webp';
}
function cloud_media_delete(string $filename): void {
 try{cloud_media_call('destroy',['public_id'=>'dream-holidays/'.substr($filename,0,-5),'invalidate'=>'true']);}catch(Throwable $ex){error_log('Cloud image cleanup failed; review unused assets in Cloudinary.');}
}
