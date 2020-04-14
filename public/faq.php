<head class="__web-inspector-hide-shortcut__"><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>CrypticVPN - FAQ</title>
	<link href="css/style.css" rel="stylesheet">
	<link href="css/font-style.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1">
<style id="__web-inspector-hide-shortcut-style__" type="text/css">
.__web-inspector-hide-shortcut__, .__web-inspector-hide-shortcut__ *, .__web-inspector-hidebefore-shortcut__::before, .__web-inspector-hideafter-shortcut__::after
{
    visibility: hidden !important;
}
</style></head>

<body>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-77945555-1', 'auto');
  ga('send', 'pageview');

</script>
<center><h3><strong>CrypticVPN Frequently Asked Questions/Answers</strong></h3></center>
<Br>
<section class="content-area contact">

    		<div class="container">
				<ol type="1">
					<li>
						<a href="#install-faq">How do I setup the VPN on Windows</a>
					</li>
					<li>
						<a href="#linux-faq">How can I use the VPN on a Mac (OSX) or Linux?</a>
					</li>
					<li>
						<a href="#portforwarding-faq">How do I portforward?</a>
					</li>
					<li>
						<a href="#authentication-faq">Auth-Failed error when connecting to the VPN. What should I do?</a>
					</li>
	
					<li>
						<a href="#ssl-faq">How do I fix SSL Errors?</a>
					</li>
					<li>
						<a href="#tapDrivers-faq">No Tap-Windows Adapters installed.</a>
					</li>
					<li>
						<a href="#dnsLeak-faq">DNS Leaking</a>
					</li>
					<li>
						<a href="#slow-faq">I get slow speeds while on the VPN</a>
					</li>
					<li>
						<a href="#plan-faq">Bought a subscription but I still have not received it yet.</a>
					</li>
					<li>
						<a href="#prtfwdBlocked-faq">Can't port forward on certain servers.</a>
					</li>
					<li>
						<a href="#libeay32">The program can't start because LIBEAY32.dll is missing from your computer.</a>
					</li>
				</ol>
				
				<div id="install-faq">
					<h2>
						How do I setup the VPN on Windows?
					</h2>
					<p>
						For Windows you have two options.  You can either use the CrypticVPN client or you can use OpenVPN GUI.
						<br>
						It is recommended to use the CrypticVPN client as it makes everything very easy.
						<br>
						<ol type="A">
							<li><strong>CrypticVPN Client</strong></li>
							Login to <a href="https://www.crypticvpn.com">CrypticVPN.com</a> and go to the Downloads Tab.
							<br>
							Download the CrypticVPN Client
							<br>
							Run the program as administrator by right clicking on it and selecting "Run as Admin".
							<br>
							You may be prompted to install a few things, hit yes.
							<br>
							This may take a few minutes.
							<br>
							Once it is all done, launch CrypticVPN <strong>as Administrator</strong> and enter your VPN credentials.
							<br>
							You should be able to connect to servers now.
							<br>
							<br><li><strong>OpenVPN GUI</strong></li>
							Download OpenVPN GUI from <a href="https://openvpn.net/index.php/open-source/downloads.html">here</a>. If you have a 32 bit operating system get the 32 bit version, if you have 64 bit operating system get the 64 bit version.
							<br>
							Install the software, then login to <a href="https://www.crypticvpn.com">CrypticVPN.com</a> and go to the Downloads Tab. Download the config zip folder.
							<br>
							Extract the files.
							<br>
							Place the files in the following directory:
							<br>
							If you installed the 32 bit version: C:\Program Files\OpenVPN\config
							<br>
							If you installed the 64 bit version: C:\Program Files (x86)\OpenVPN\config
							<br>
							Launch OpenVPN-GUI as Admin
							<br>
							Right click on the OpenVPN-GUI icon in your task bar and you should be able to connect to the different servers.
						</ol>


		
							
					</p>
				</div>
				
				
				<div id="portforwarding-faq">
					<h2>
						How do I portforward with this VPN?
					</h2>
					<p>
						<strong> Before proceeding with the instructions below, make sure you <u>DISABLE</u> your firewall, or the port will keep saying its closed.</strong>
						<br>
						An easy to follow video tutorial can be found <a href="https://www.youtube.com/watch?v=NkK98bEwFhk">here</a>.
						<br>
						After you login to the CrypticVPN website, click on "VPN Control Panel" in the sidebar. 
						<br>
						Then go to the "Port Forwarding tab" 
						<br>
						You will need your internal VPN IP. Connect to the VPN location you would like to open a port on, then go to command prompt and run 'ipconfig'. 
						<br>
						Look for the IP that begins with 10.8.x.x. (IP will vary by location)<br>
						<br>
						Once you open the port, use a website like <a href="http://canyouseeme.org/">CanYouSeeMe.org</a> to check if it is open.
						<br>
						Make sure something is listening on the port before checking it on CanYouSeeMe.org, we recommend using the Port Listener tool which can be found in the Downloads section on our website.

					</p>
				</div>
				
				<div id="linux-faq">
					<h2>
						Can I use this VPN on a Mac OSX or Linux?
					</h2>
					<p>
						For Mac OSX <a href="https://tunnelblick.net/">TunnelBlick</a>. Once it is installed, download the config files from our website and import them into TunnelBlick. Then login with your VPN credentials.
					</p>
					<p>For Linux, you need to install OpenVPN and use the config files that can be downloaded from our website.</p>
				</div>
				
				<div id="authentication-faq">
					<h2>
						I get an AUTH failed error, what do I do?
					</h2>
					<p>
						If you are getting an authentication failure error, make sure you are using your VPN credentials and not the website login credentials. Try connecting to another VPN location, if the issue still persists, contact support.
					</p>
				</div>
    		
				<div id="ssl-faq">
					<h2>
						I am getting an SSL Error, how can I fix it?
					</h2>
					<p>
						Sometimes certain websites display SSL errors when someone is browsing from a VPN. This can be due to the fact that your browser does not support SNI (Server Name Indication) as some older browsers do not support this. 
						<br>
						To fix it you will need to use a different or up-to-date browser. If the issue still persists, please contact support.
					</p>
				</div>
				
				<div id="tapDrivers-faq">
					<h2>
						No Tap-Windows Adapters installed.
					</h2>
					<p>
						Uninstall all instances of OpenVPN on your machine.
						Download the latest version of OpenVPN from<a href="https://openvpn.net/index.php/open-source/downloads.html">here</a>.
						Make sure it installs a TAP driver/adapter. 
					</p>
				</div>
				
				<div id="dnsLeak-faq">
					<h2>
						I have a DNS leak, how do I fix this?
					</h2>
					<p>
						The best way to fix a DNS leak is to manually configure your own DNS servers. Go to your Network Adapter properties and set it to Google's. You can also set a DNS server on your router. Please make sure that you clear your DNS cache after doing this. (Open CMD &gt; ipconfig /flushdns)  
						<br>
						More details on setting your DNS servers to Google's can be found <a href="https://developers.google.com/speed/public-dns/docs/using">here</a>
					</p>
				</div>
				
				<div id="slow-faq">
					<h2>
						I get slow speeds while on the VPN
					</h2>
					<p>
						All of our servers are connected a minimum of a 1 Gbit Uplink, but that does not mean you will get 1 Gbit speeds.
						<br>
						When using a VPN, you will never get speeds faster than what you are paying your ISP for.
						<br>
						There are many factors that come into play when determining what speeds you will get a VPN. First and foremost, when using a VPN the bandwidth and throughout of the server you are connecting to is shared amongst all the users that are connected to the server at the time. Second of all, the distance between the servers physical location and your location plays a big role, as the further away you are, the greater the latency.
						<br>
						We highly recommend using the server closest to you to get the best speeds possible. 
						<br>
						You should also turn off services that use the internet that you do not need when using the VPN. An example would be torrent clients. Seeding torrents when connected to the VPN will drastically reduce your network performance.
					</p>
				</div>
				
				<div id="plan-faq">
					<h2>
						I bought a subscription but I still have not received it yet.
					</h2>
					<p>
						If you paid via PayPal, you most likely did not wait for PayPal to redirect you to our website. Open a ticket with your transaction ID and PayPal email and we will manually provision your account.
						<Br>
						If you paid with BitCoin, our payment processor CoinPayment's waits for 2 transactions. Depending on the performance of the BitCoin network this can take anywhere from 10 minutes to 2 hours. If it has been more than 2 hours, open a support ticket.
					</p>
				</div>
				
				<div id="prtfwdBlocked-faq">
					<h2>
					I can't port forward on certain servers.					
					</h2>
					<p>
						Sometimes we are forced to disable port-forwarding on certain servers due to a high number of abuse reports. These restrictions are generally temporary, and lifted as soon as possible. 
						<Br>
						To avoid getting port-forward disabled, please refrain from downloading copy-righted material such as Movies/TV Show torrents on non-offshore servers.
					</p>
				</div>
    		
    			<div id="libeay32">
					<h2>
					The program can't start because LIBEAY32.dll is missing from your computer.
					</h2>
					<p>
					This error happens occasionally when you have a DLL file mising from your computer, which prevents OpenVPN from running. It is an easy fix. <a href="http://www.dlldownloader.com/libeay32-dll/">Click here</a> to visit a website that provides the DLL file and step by step instructions on how to install it.
					</p>
				</div>

    	</section>
</body>


<footer>
				<div class="container">
					<div class="div_footer">
						<ul class="footer-section">
							<li class="title">QUICK NAVIGATION</li>
							<li class="divider"></li>
							<li><a href="index.php">Home</a></li>
							<li><a href="#whychoose">Why Choose Us</a></li>
							<li><a href="#locations">Locations</a></li>
							<li><a href="#pricing">Pricing</a></li>
							<li><a href="contact.php">Contact Us</a></li>
							<li><a href="login.php">Client Area</a></li>
						</ul>
						<ul class="footer-section">
							<li class="title">User Agreement</li>
							<li class="divider"></li>
							<li><a href="tos.php">Terms of Service</a></li>
							<li><a href="privacy.php">Privacy Policy</a></li>
						</ul>
					 
						<ul class="right nmr footer-section">
							<li class="title"><a href="index.php"><img src="img/logo.png"></a></li>
							<li class="divider"></li>
							<li style="">
								<a href="https://www.facebook.com/CrypticVPN/"><img src="img/icon/icon_fb.png"></a>
								<a href="https://twitter.com/CrypticVPN"><img src="img/icon/icon_tw.png"></a>
								<a href="#"><img src="img/icon/icon_gg.png"></a>
							</li>
						</ul>
					</div>
					
				</div>
			</footer>