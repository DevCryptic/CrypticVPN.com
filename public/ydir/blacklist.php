<?php
ob_start();
require_once '../application/config.php';
require_once '../application/global.php';

if (!($user -> IsAdmin($odb)))
{
	header('Location: https://crypticvpn.com/ydir');
	die();
}



?>

<!DOCTYPE html>
<html lang="en">
  <title><?php echo $site_title; ?> - Port Blacklist Management</title>
<?php //include('../templates/head.php'); ?>
<body>
    <?php //include('../templates/header.php'); ?>
    <div class="container">
<?php
                if (isset($_POST['addBlacklist']))
                {
                    $port = $_POST['port'];
                    $errors = array();

                        if (empty($port))
                        {
                            $errors[] = 'Please fill in all fields.';
                        }

                        if(!is_numeric($port)) {
                            $errors[] = 'Invalid port number.';
                        }

                        if(empty($errors)) {
                            $insertPackage = $odb -> prepare("INSERT INTO `blacklist` VALUES (NULL, :port)");
                            $insertPackage -> execute(array(':port' => $port));
                            echo '<div class="uk-alert uk-alert-success" data-uk-alert><a href="#" class="uk-alert-close uk-close"></a> You have added a new port blacklist successfully!</div>';
                        } else {
                            echo '<div class="uk-alert uk-alert-danger" data-uk-alert><a href="#" class="uk-alert-close uk-close"></a>';
                            foreach($errors as $error)
                            {
                                echo '- '.$error.'<br />';
                            }
                            echo '</div>';
                        }
                }
                if (isset($_GET['del']))
                {
                    if (is_numeric($_GET['del'])){
                        $checkIfExists = $odb -> prepare("SELECT * FROM `blacklist` WHERE `id` = :nid");
                        $checkIfExists -> execute(array(':nid' => $_GET['del']));
                        if($checkIfExists -> rowCount() == 0)
                        {
                            header('location: index.php');
                            die();
                        } else {
                            $delete = $_GET['del'];
                        $SQL = $odb -> prepare("DELETE FROM `blacklist` WHERE `id` = :id");
                        $SQL -> execute(array(':id' => $delete));
                        echo '<div class="uk-alert uk-alert-success" data-uk-alert><a href="#" class="uk-alert-close uk-close"></a> You have deleted the blacklist!</div>';
                        }
                    } else {
                        echo '<div class="uk-alert uk-alert-danger" data-uk-alert><a href="#" class="uk-alert-close uk-close"></a> Invalid delete. </div>';
                    }
                }
            ?>   

            <div class="md-card">
            <form method="post">
                <div class="md-card-content">
                    <h3 class="heading_a">Blacklist a Port</h3>
                    <div class="uk-grid" data-uk-grid-margin>
                        <div class="uk-width-medium-1-1">
                            <div class="uk-form-row">
                                <div class="uk-grid">
                                    <div class="uk-width-medium-1-2">
                                        <label>Port Number</label>
                                        <input type="text" name="port" class="md-input label-fixed" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="uk-grid">
                        <div class="uk-width-1-1">
                            <button type="submit" name="addBlacklist" class="md-btn md-btn-primary">Submit</button>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
            <br>
            <h4 class="heading_a uk-margin-bottom">Manage Blacklists</h4>
            <div class="md-card uk-margin-medium-bottom">
                <div class="md-card-content">
                    <div class="uk-overflow-container">
                        <table class="uk-table">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Port</th>
                                <th>Manage</th>
                            </tr>
                            </thead>
                            <tbody>
                           <?php
                                $GetPackages = $odb -> prepare("SELECT * FROM `blacklist` ORDER BY `id` DESC LIMIT 100");
                                $GetPackages -> execute();
                                while ($getInfo = $GetPackages -> fetch(PDO::FETCH_ASSOC))
                                {
                                    $id = $getInfo['id'];
                                    $port = $getInfo['port'];
                                    echo '
                                    <tr>
                                        <td>'.$id.'</td>
                                        <td>'.$port.'</td>
                                        <td><a class="md-btn md-btn-danger" href="blacklist.php?del='.$id.'">Delete</a></td>
                                    </tr>';
                                }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- google web fonts -->
    <script>
        WebFontConfig = {
            google: {
                families: [
                    'Source+Code+Pro:400,700:latin',
                    'Roboto:400,300,500,700,400italic:latin'
                ]
            }
        };
        (function() {
            var wf = document.createElement('script');
            wf.src = ('https:' == document.location.protocol ? 'https' : 'http') +
            '://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
            wf.type = 'text/javascript';
            wf.async = 'true';
            var s = document.getElementsByTagName('script')[0];
            s.parentNode.insertBefore(wf, s);
        })();
    </script>

    <!-- common functions -->
    <script src="assets/js/common.min.js"></script>
    <!-- uikit functions -->
    <script src="assets/js/uikit_custom.min.js"></script>
    <!-- altair common functions/helpers -->
    <script src="assets/js/altair_admin_common.min.js"></script>

    <!-- page specific plugins -->
       
        <script src="http://maps.google.com/maps/api/js?sensor=true"></script>

        <script src="assets/js/custom/handlebars_helpers.min.js"></script>
        <!-- CLNDR -->
      
        <!--  dashbord functions -->
        <script src="assets/js/pages/dashboard.min.js"></script>

    <script>
        $(function() {
            // enable hires images
            altair_helpers.retina_images();
            // fastClick (touch devices)
            if(Modernizr.touch) {
                FastClick.attach(document.body);
            }
        });
    </script>
</body>
</html>
</html>