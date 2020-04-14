<!-- statistics (small charts) -->
            <div class="uk-grid uk-grid-width-large-1-4 uk-grid-width-medium-1-2 uk-grid-medium hierarchical_show">
 				<div>
                    <div class="md-card">
                        <div class="md-card-content">
                            <div class="uk-float-right uk-margin-top uk-margin-small-right"><i class="material-icons">&#xE0C6;</i></div>
                            <span class="uk-text-muted uk-text-small">Pending Tickets</span>
                            <h2 class="uk-margin-remove"><span class="countUpMe"><?php echo $gsetting -> pendingTickets($odb); ?></span></h2>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="md-card">
                        <div class="md-card-content">
                            <div class="uk-float-right uk-margin-top uk-margin-small-right"><i class="material-icons">&#xE227;</i></div>
                            <span class="uk-text-muted uk-text-small">Sales Today </span>
                            <h2 class="uk-margin-remove"><!--<span class="countUpMe">-->$<?php if ($gsetting -> earnedToday($odb)=='') echo "0"; else echo ($gsetting -> earnedToday($odb))?> USD</span></h2>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="md-card">
                        <div class="md-card-content">
                            <div class="uk-float-right uk-margin-top uk-margin-small-right"><i class="material-icons">&#xE227;</i></div>
                            <span class="uk-text-muted uk-text-small">Sales Yesterday </span>
                            <h2 class="uk-margin-remove"><!--<span class="countUpMe">-->$<?php echo $gsetting -> earnedYesterday($odb); ?> USD</span></h2>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="md-card">
                        <div class="md-card-content">
                            <div class="uk-float-right uk-margin-top uk-margin-small-right"><i class="material-icons">&#xE227;</i></div>
                            <span class="uk-text-muted uk-text-small">Sales Overall </span>
                            <h2 class="uk-margin-remove"><!--<span class="countUpMe">-->$<?php echo $gsetting -> earnedOverall($odb); ?> USD</span></h2>
                        </div>
                    </div>
                </div>
               
                <!--<div>
                    <div class="md-card">
                        <div class="md-card-content">
                            <div class="uk-float-right uk-margin-top uk-margin-small-right"><i class="material-icons">&#xE863;</i></div>
                            <span class="uk-text-muted uk-text-small">Pending Actions</span>
                            <h2 class="uk-margin-remove"><span class="countUpMe">php echo $gsetting -> pendingActions($odb); </span></h2>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="md-card">
                        <div class="md-card-content">
                            <div class="uk-float-right uk-margin-top uk-margin-small-right"><i class="material-icons">&#xE32A;</i></div>
                            <span class="uk-text-muted uk-text-small">Pending Dedicated Orders</span>
                            <h2 class="uk-margin-remove"><span class="countUpMe">php echo $gsetting -> dedicatedOrders($odb); </span></h2>
                        </div>
                    </div>
                </div>-->
            </div>
