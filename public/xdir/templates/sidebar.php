<!-- main sidebar -->
    <aside id="sidebar_main">

        <div class="sidebar_main_header">
            <div class="sidebar_logo">
                <a href="index.php" class="sSidebar_hide"><img src="assets/img/logo_main.png" alt="" height="25" width="120"/></a>
            </div>
        </div>

        <div class="menu_section">
            <ul>
                <li class="<?php CheckPageAdmin('index.php'); ?>" title="Dashboard">
                    <a href="index.php">
                        <span class="menu_icon"><i class="material-icons">&#xE871;</i></span>
                        <span class="menu_title">Dashboard</span>
                    </a>
                </li>
				        <li class="<?php CheckPageAdmin('users.php'); ?>" title="User Management">
                    <a href="users.php">
                        <span class="menu_icon"><i class="material-icons">&#xE87C;</i></span>
                        <span class="menu_title">User Management</span>
                    </a>
                </li>
				        <li class="<?php CheckPageAdmin('servers.php') || CheckPageAdmin('viewserver.php') || CheckPageAdmin('newserver.php'); ?>" title="Server Management">
                    <a href="servers.php">
                        <span class="menu_icon"><i class="material-icons">&#xE875;</i></span>
                        <span class="menu_title">Server Management</span>
                    </a>
                </li>
                <li class="<?php CheckPageAdmin('packages.php') || CheckPageAdmin('viewpackage.php'); ?>">
                    <a href="packages.php">
                        <span class="menu_icon"><i class="material-icons">&#xE8CB;</i></span>
                        <span class="menu_title">Manage Packages</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <span class="menu_icon"><i class="material-icons">&#xE32A;</i></span>
                        <span class="menu_title">Manage Private VPN</span>
                    </a>
                    <ul>
          						<li class="<?php CheckPageB('dedipackage.php') || CheckPageB('viewdpackage.php'); ?>"><a href="dedipackage.php">Dedicated Packages</a></li>
          						<li class="<?php CheckPageB('dedicated.php') || CheckPageB('newdedi.php') || CheckPageB('viewdserver.php'); ?>"><a href="dedicated.php">Dedicated Servers</a></li>
          						<li class="<?php CheckPageB('dedipayments.php'); ?>"><a href="dedipayments.php">Dedicated Payments</a></li>
          						<li class="<?php CheckPageB('dedipending.php'); ?>"><a href="dedipending.php">Pending Setup</a></li>
                    </ul>
                </li>
                <li>
                    <a href="#">
                        <span class="menu_icon"><i class="material-icons">&#xE896;</i></span>
                        <span class="menu_title">Miscellaneous</span>
                    </a>
                    <ul>
          						<li class="<?php CheckPageB('massmail.php'); ?>"><a href="massmail.php">Send Mass Mail</a></li>
          						<li class="<?php CheckPageB('news.php'); ?>"><a href="news.php">Manage News</a></li>
          						<li class="<?php CheckPageB('giftcodes.php'); ?>"><a href="giftcodes.php">Gift Codes</a></li>
          						<li class="<?php CheckPageB('blacklist.php'); ?>"><a href="blacklist.php">Blacklist Port</a></li>
          						<li class="<?php CheckPageB('paymentlogs.php'); ?>"><a href="paymentlogs.php">Payment Logs</a></li>
                    </ul>
                </li>
				        <li class="<?php CheckPageAdmin('settings.php'); ?>" title="General Settings">
                    <a href="settings.php">
                        <span class="menu_icon"><i class="material-icons">&#xE8B8;</i></span>
                        <span class="menu_title">General Settings</span>
                    </a>
                </li>
				        <li class="<?php CheckPageAdmin('tickets.php') || CheckPageAdmin('viewticket.php'); ?>" title="Tickets">
                    <a href="tickets.php">
                        <span class="menu_icon"><i class="material-icons">&#xE0C6;</i></span>
                        <span class="menu_title">Tickets</span>
                    </a>
                </li>
            </ul>
        </div>
    </aside><!-- main sidebar end -->
