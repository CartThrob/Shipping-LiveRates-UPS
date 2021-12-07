<?php

$lang = [
    'ups_live_rates' => 'UPS Live Rates',
    'ups_overview' => '<h3 style="display:block">Use of This Plugin</h3>
	
	<p>LiveRates shipping costs are not updated automatically when the cart contents are modified. LiveRates shipping plugins requires that you manually request a shipping quote at some point during your checkout process. If you do not use the <a href="http://cartthrob.com/docs/tags_detail/get_shipping_options/index.html">{exp:cartthrob:get_shipping_options}</a> tag, your shipping costs may not be set for checkout.</p> 
	<p>For more details about this and other live rates plugins see: http://cartthrob.com/docs/tags_detail/get_live_rates_form/</p>
	<h3 style="display:block">Estimate Accuracy Warning</h3>
	<p>If your actual packing and shipping methods differ from the information you use to request the cost estimate, your shipping costs may vary from the estimated value. By default the entire cart weight is used to calculate shipping costs, along with default length, width, and height values set below. If you require more specialized packaging, please post a request in the CartThrob forums. If modifications to this plugin are required, additional fees may be incurred. </p>
	<p>Each time items in the cart are added, updated, or removed, shipping costs are reset to zero when using a LiveRates shipping plugin. It is recommended that you check to see that shipping costs are set before allowing a customer to check out. For example: {if "{exp:cartthrob:cart_shipping prefix=""}" = "0.00"}show live rates{if:else}show checkout{/if}</p>
	<h3 style="display:block">Dimensional Weight Warning</h3><p>Length + Width + Height values are <strong>required</strong> for shipping quotes using LiveRates plugins. Some shipping methods such as overnight, next-day, and two-day shipping methods may only calculate costs by box dimensions rather than standard weights. Please consult documentation provided by your shipping company, or your shipping representative for more information.</p>',
];
