<?php
ob_start();
require_once '../application/config.php';
require_once '../application/global.php';

if (!($user -> IsAdmin($odb)))
{
	header('Location: https://crypticvpn.com/ydir');
	die();
}

function rVPNGiftCode()
{
    $prefix = $_POST['prefix'];
    $template   = '-XX99XX-9XX9-9999-XXXX-X99X-99XX99';
    $k = strlen($template);
    $couponcode = '';
    for ($i=0; $i<$k; $i++)
    {
        switch($template[$i])
        {
            case 'X': $couponcode .= chr(rand(65,90)); break;
            case '9': $couponcode .= rand(0,9); break;
            case '-': $couponcode .= '-';  break;
        }
    }
    return $prefix.$couponcode;
}



?>

<!DOCTYPE html>
<html lang="en">
  <title><?php echo $site_title; ?> - Gift Code Management</title>
<?php //include('../templates/head.php'); ?>
<body>
    <?php //include('../templates/header.php'); ?>
    <div class="container">
      <?php
                if (isset($_POST['generateCodes'])){
                    $length = $_POST['length'];
                    $unit = $_POST['unit'];
                    $codeamounts = $_POST['amount'];
                    $prefix = $_POST['prefix'];
                    $errors = array();

                    if (empty($length) || empty($unit) || empty($codeamounts)){
                        echo '<div class="alert-danger" data-alert><a href="#" class="alert-close close"></a> Please fill in all fields. </div>';
                    } else {
                        $codes = [];
                        $insert1 = $odb -> prepare("INSERT INTO `giftcodes` VALUES (NULL, :code, :length, :unit, 0)");
                        for ($i = 1; $i <= $codeamounts; ++$i) {
                            $code = rVPNGiftCode($prefix);
                            $insert1 -> execute(array(':code' => $code, ':length' => $length, ':unit' => $unit));
                            $codes[] = $code;
                        }
                        echo '<div class="alert alert-success" data-alert><a href="#" class="alert-close close"></a>'.implode('<br>', $codes).'</div>';
                    }
                }
            ?>
                  <div class="wrap-main">

            <div class="md-card">
                <form method="post">
                    <div class="md-card-content">
                        <ul class="tab tab-grid" data-tab="{connect:'#tabs_4'}">
                            <li class="width-1-2 active"><a href="#">Generate Codes</a></li>
                            <li class="width-1-2"><a href="#">Codes</a></li>
                        </ul>
                        <ul id="tabs_4" class="switcher margin">
                            <li>
                                <br>
                                <div class="grid" data-grid-margin>
                                    <div class="width-medium-1-1">
                                        <div class="form-row">
                                            <div class="grid">
                                                <div class="width-medium-1-2">
                                                    <label>Length</label>
                                                    <input type="text" name="length" class="md-input label-fixed" />
                                                </div>
                                                <div class="width-medium-1-2">
                                                    <select id="select_demo_1" name="unit" data-md-selectize>
                                                        <option value="">Select Unit..</option>
                                                        <option value="Days">Day(s)</option>
                                                        <option value="Weeks">Week(s)</option>
                                                        <option value="Months">Month(s)</option>
                                                        <option value="Years">Year(s)</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="grid">
                                                <div class="width-medium-1-2">
                                                    <label>Prefix</label>
                                                    <input type="text" name="prefix" class="md-input label-fixed" placeholder="C-" />
                                                </div>
                                                <div class="width-medium-1-2">
                                                    <label>Amount of Codes</label>
                                                    <input type="text" name="amount" class="md-input label-fixed" placeholder="50" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="grid">
                                    <div class="width-1-1">
                                        <button type="submit" name="generateCodes" class="md-btn md-btn-primary">Generate</button>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <?php
                                    $checkIfExists = $odb -> prepare("SELECT * FROM `giftcodes`");
                                    $checkIfExists -> execute();
                                    if($checkIfExists -> rowCount() == 0)
                                    {
                                        echo '<i> - No Codes Available - </i>';
                                    } else {
                                ?>
                                    <div class="md-card-content">
                                        <div class="overflow-container">
                                            <table class="table table-nowrap">
                                                <thead>
                                                <tr>
                                                    <th class="width-2-10">ID</th>
                                                    <th class="width-2-10 text-center">Code</th>
                                                    <th class="width-2-10 text-center">Length</th>
                                                    <th class="width-2-10 text-center">Unit</th>
                                                    <th class="width-2-10 text-center">Used By</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                        $GetCodes = $odb -> prepare("SELECT * FROM `giftcodes` ORDER BY `id` DESC LIMIT 100");
                                                        $GetCodes -> execute();
                                                        while ($getInfo = $GetCodes -> fetch(PDO::FETCH_ASSOC))
                                                        {
                                                            $id = $getInfo['id'];
                                                            $code = $getInfo['code'];
                                                            $length = $getInfo['length'];
                                                            $unit = $getInfo['unit'];
                                                            $usedby = $getInfo['usedby'];

                                                            echo '
                                                                <tr>
                                                                    <td>'.$id.'</td>
                                                                    <td class="text-center">'.$code.'</td>
                                                                    <td class="text-center">'.$length.'</td>
                                                                    <td class="text-center">'.$unit.'</td>
                                                                    <td class="text-center">'.$usedby.'</td>
                                                                </tr>
                                                            ';
                                                        }
                                                ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                <?php } ?>
                            </li>
                        </ul>
                    </div>
                </form>
            </div>

        </div>
    </div>
    </div>

    </div><!-- /.container -->

    <?php include('../templates/footer.php'); ?>
	<?php include('../templates/scriptjs.php'); ?>
	
</html>