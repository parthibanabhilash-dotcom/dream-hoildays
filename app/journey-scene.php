<?php
$theme=match($route){'packages'=>'islands','vehicles'=>'road','gallery'=>'gallery','contact'=>'flight','about'=>'camp',''=>'road',default=>isset($p)&&$p?'islands':'camp'};
$themes=[
 'road'=>['THE OPEN ROAD','Take the long way.','Enjoy every turn.','Winding roads, green hills and a journey worth slowing down for.','Hill road','Find your ride','ON THE ROAD'],
 'islands'=>['ISLAND STATE OF MIND','A little further.','A little freer.','Blue horizons, tiny islands and room for a different kind of escape.','Island hopping','Plan your escape','CHASE HORIZONS'],
 'gallery'=>['COLLECT THE MOMENTS','Little frames.','Big memories.','A floating collection of places, colours and moments to inspire your next chapter.','Through the lens','Explore the gallery','STAY CURIOUS'],
 'flight'=>['YOUR NEXT DEPARTURE','Dream it here.','Take it anywhere.','Every adventure starts with a conversation. Tell us where your imagination is taking you.','Ready for takeoff','Start a conversation','NEXT STOP: YOU'],
 'camp'=>['CLOSER TO NATURE','Less rush.','More wonder.','A quiet mountain camp, a slower pace and a fresh perspective on the journey ahead.','Under the stars','Our travel approach','WANDER SLOWLY']
];$j=$themes[$theme];
?>
<section class="journey-world container theme-<?=e($theme)?>" aria-labelledby="journey-title">
 <div class="journey-heading"><div><span class="eyebrow"><?=e($j[0])?></span><h2 id="journey-title"><?=e($j[1])?><br><em><?=e($j[2])?></em></h2></div><p><?=e($j[3])?></p></div>
 <div class="journey-landscape">
  <div class="journey-sun" aria-hidden="true"></div><span class="journey-coordinate" aria-hidden="true">THE DREAM ATLAS / <?=e(strtoupper($theme))?></span>
  <img class="journey-fallback" src="<?=e(asset('assets/scene-'.$theme.'.svg'))?>" alt="<?=e($j[4])?> travel illustration" width="1200" height="550" loading="lazy">
  <div id="journey-canvas" data-theme="<?=e($theme)?>" aria-hidden="true"></div>
  <a class="journey-pin pin-hills" href="<?=e(url($theme==='road'?'vehicles':($theme==='gallery'?'gallery':'packages')))?>"><span>01</span><div><?=e($j[4])?><small>Explore a new perspective ↗</small></div></a>
  <a class="journey-pin pin-flight" href="<?=e(url('contact'))?>#enquiry"><span>02</span><div><?=e($j[5])?><small>Make it yours ↗</small></div></a>
  <div class="journey-stamp">THE DREAM HOLIDAYS<br><strong><?=e($j[6])?></strong></div><span class="journey-note">An illustrated journey. Your itinerary starts with you.</span>
 </div>
</section>
