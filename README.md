Hi, I am Cryptic, the original creator of CrypticVPN. 
Here is my version of the source, prior to it being sold in April 2018.

The reason why I am making it public is for educational purposes and to prevent the tons of crappy sources from being sold online.

The CrypticVPN source is a heavily modified version of rVPN, I have customized it and improved it in big ways.

CrypticVPN had 4 main servers which were VPS's.

It's best to have 5 VPS's instead of running multiple sites on the same one for security purposes


VPS #1 CrypticVPN.com 
This VPS is for the main website and hosts only the website database. I used a web-stack called CentMinMod (https://centminmod.com/)

VPS #2 Downloads.CrypticVPN.com
This VPS is a basic web-server with the client / config files availiable for download. Basic Nginx or Apache setup, no database required.

VPS #3 ClientConnect.CrypticVPN.com
This VPS serves mainly the CrypticVPN desktop clien. Basic Nginx or Apache setup, no database required.

VPS #4 FreeRadius Server
Setup a FreeRadius server on a separate VPS, the only thing running on this VPN should be the FreeRadiusServer
The document I followed for years is https://web.archive.org/web/20160529054128/http://safesrv.net/install-and-setup-freeradius-on-centos-5
Once complete, import FreeRadiusServerDB.sql
It is important not to run the FreeRadius server on the same server as your website or ClientConnect or Downloads
The IP of this server should always be hidden, no one connects to this server directly as all pass-through is done via the ClientConnect API. If this server goes down, your entire VPN system goes down, you can look into setting up fail-overs.
You do not need any web-server installed on this server.

VPS #5 Backups
The purpose of this VPS is to backup all the other VPS's on a daily basis. No webserver install required.
It's good to have an automated backup system. I used rSync and wrote a script that took daily backups of the websites/databases running on the other VPS's and uploaded it to this server.
#
#
Sample backup script
#mysqldump -u backups -p'^UJVV2*Eb$8*8Dkz*g^4' cvpn_data > /home/backups/raw/cvpn_data-`date '+%y%m%d_%H%M%S'`.sql
#tar -cjvf /home/backups/raw/crypticvpn.com-`date '+%y%m%d_%H%M%S'`.tar.bz2 -C /home/nginx/domains/crypticvpn.com .
#tar -cjvf /home/backups/packed/crypticVPNPacked-`date '+%y%m%d_%H%M%S'`.tar.bz2 -C /home/backups/raw .
#rm -rf /home/backups/raw/*
#rsync -azP --remove-source-files /home/backups/packed/ backups@107.191.102.198:/home/backups/crypticvpn.com/
#

On all servers it's best to have the best security practices which include disabling password based SSH logins, changing the default SSH port, enabling authentication via SSH Key only (ssh-copy-id), disabling remote root login, etc.

When I was running the business with 4000+ active customers, all VPS's were obtained from RamNode, and were pretty inexpensive.
https://clientarea.ramnode.com/aff.php?aff=699
