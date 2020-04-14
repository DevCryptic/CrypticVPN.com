<?php
ob_start();
require_once '../application/config.php';
require_once '../application/global.php';

if (!($user -> IsAdmin($odb)))
{
    header('Location: https://crypticvpn.com/ydir');
    die();
}

if (isset($_GET['id'])) {
    if (is_numeric($_GET['id'])) {
        $uid = $_GET['id'];
        //Get required user info to edit
        $SQLGetInfo = $odb -> prepare("SELECT * FROM `accounts` WHERE `id` = :userid LIMIT 1");
        $SQLGetInfo -> execute(array(':userid' => $_GET['id']));
        $userInfo = $SQLGetInfo -> fetch(PDO::FETCH_ASSOC);
        $userid = $userInfo['id'];
        $username = $userInfo['username'];
        $emailaddr = $userInfo['email'];
        $isactive = $userInfo['isactive'];
        $isbanned = $userInfo['isbanned'];
        $downtimenotification = $userInfo['receive_dtinfo'];
        $specialoffers = $userInfo['special_offers'];
        $portfwoption = $userInfo['pfoption'];
        $expire = $userInfo['expire'];
        $creation_date = $userInfo['acdate'];


    } else {
        header('Location: users.php');
        die();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
  <title><?php echo $site_title; ?> - User Management</title>
<?php include('../templates/head.php'); ?>
<body>
    <?php include('../templates/header.php'); ?>
    <div class="container">
      <div class="wrap-main">
        <div class="main-contain col-sm-11">
            <div class="left-content col-sm-3">
                <?php include('../templates/sidebar_left_adm.php'); ?>
            </div>

            <div class="right-content col-sm-9">

                <div class="tab-content responsive">
                    <? 
    if (isset($_POST['updateUser'])) {
                    $update = false;
                    $errors = array();
                    if (!empty($_POST['password']))
                    {
                        $SQL = $odb -> prepare("UPDATE `accounts` SET `password` = :password WHERE `id` = :id");
                        $SQL -> execute(array(':password' => MD5($_POST['password']), ':id' => $uid));
                        $update = true;
                    }
                    if ($username != $_POST['username'])
                    {
                        $SQL = $odb -> prepare("UPDATE `accounts` SET `username` = :username WHERE `id` = :id");
                        $SQL -> execute(array(':username' => $_POST['username'], ':id' => $uid));
                        $update = true;
                        $username = $_POST['username'];
                    }
                    if ($emailaddr != $_POST['email'])
                    {
                        $SQL = $odb -> prepare("UPDATE `accounts` SET `email` = :email WHERE `id` = :id");
                        $SQL -> execute(array(':email' => $_POST['email'], ':id' => $uid));
                        $update = true;
                        $emailaddr = $_POST['email'];
                    }
                    if (isset($_POST['isbanned']))
                    {
                        $rvpn -> deleteVPNAccount($rad, $uid, $encryptionKey);

                        $mailcontent = 'Hello, <br><br> Your account was banned from our service due to violating our TOS.<br><br> - Admin!';
                        $from = $gsetting -> getSiteMail($odb);
                        $fromname = $gsetting -> getSiteTitle($odb);
                        $mail = new PHPMailer;
                        if ($rvpnmailer == 'php') {
                            $mail->isSendmail();
                        } else {
                            $mail->isSMTP();
                            $mail->SMTPSecure = $smtpauthtype;
                            $mail->Host = $rshost;
                            $mail->Port = $rsport;
                            $mail->SMTPAuth = $smtpauthstat;
                            $mail->Username = $rsuser;
                            $mail->Password = $rspass;
                        }
                        $mail->setFrom($from, $fromname);
                        $mail->addReplyTo($from, $fromname);
                        $mail->addAddress($emailaddr, $username);
                        $mail->Subject = 'Your Have Been Banned';
                        $mail->msgHTML($mailcontent);
                        $mail->send();
                        $SQL = $odb -> prepare("UPDATE `accounts` SET `isbanned` = 1, `rVPN` = 0, `expire` = 0 WHERE `id` = :id");
                        $SQL -> execute(array(':id' => $uid));
                        $update = true;
                        $isbanned = 1;
                    } else {
                        $SQL = $odb -> prepare("UPDATE `accounts` SET `isbanned` = 0 WHERE `id` = :id");
                        $SQL -> execute(array(':id' => $uid));
                        $update = true;
                        $isbanned = 0;
                    }
                    if (isset($_POST['portfwoption']))
                    {
                        $SQL = $odb -> prepare("UPDATE `accounts` SET `pfoption` = 1 WHERE `id` = :id");
                        $SQL -> execute(array(':id' => $uid));
                        $update = true;
                        $portfwoption = 1;
                    } else {
                        $SQL = $odb -> prepare("UPDATE `accounts` SET `pfoption` = 0 WHERE `id` = :id");
                        $SQL -> execute(array(':id' => $uid));
                        $update = true;
                        $portfwoption = 0;
                    }
                
                    if (isset($_POST['isactive']))
                    {
                        $SQL = $odb -> prepare("UPDATE `accounts` SET `isactive` = 1 WHERE `id` = :id");
                        $SQL -> execute(array(':id' => $uid));
                        $update = true;
                        $isactive = 1;
                    } else {
                        $SQL = $odb -> prepare("UPDATE `accounts` SET `isactive` = 0 WHERE `id` = :id");
                        $SQL -> execute(array(':id' => $uid));
                        $update = true;
                        $isactive = 0;
                    }
                    if (isset($_POST['dtnotification']))
                    {
                        $SQL = $odb -> prepare("UPDATE `accounts` SET `receive_dtinfo` = 1 WHERE `id` = :id");
                        $SQL -> execute(array(':id' => $uid));
                        $update = true;
                        $downtimenotification = 1;
                    } else {
                        $SQL = $odb -> prepare("UPDATE `accounts` SET `receive_dtinfo` = 0 WHERE `id` = :id");
                        $SQL -> execute(array(':id' => $uid));
                        $update = true;
                        $downtimenotification = 0;
                    }
                    if (isset($_POST['specialoffers']))
                    {
                        $SQL = $odb -> prepare("UPDATE `accounts` SET `special_offers` = 1 WHERE `id` = :id");
                        $SQL -> execute(array(':id' => $uid));
                        $update = true;
                        $specialoffers = 1;
                    } else {
                        $SQL = $odb -> prepare("UPDATE `accounts` SET `special_offers` = 0 WHERE `id` = :id");
                        $SQL -> execute(array(':id' => $uid));
                        $update = true;
                        $specialoffers = 0;
                    }

                    if ($update == true)
                    {
                        echo '<div class="alert alert-success" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> Update Sucess.</div>';
                    }
                    else
                    {
                        echo '<div class="alert alert-danger" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> No changes were made.</div>';
                    }
                }?>

                <?php if (isset($_GET['id'])) { ?>
                 
                <strong>Subscription Valid Until: </strong>  <?php echo ($expire != 0 ? date("m-d-Y h:i:s A", $expire) : 'Expired!');?>

                 <div class="tab-content responsive">
                   <div class="table-responsive">
                    <form method="post">
                      <div class="form-group">
                        <label for="subject">Username</label>
                        <input type="text" class="form-control" name="username" id="username" value="<?php echo $username; ?>">
                      </div> 
                      <div class="form-group">
                        <label for="subject">Update Password</label>
                        <input type="password" class="form-control" id="password" name="password" value="">
                      </div> 
                      <div class="form-group">
                        <label for="subject">Email Address</label>
                        <input type="text" class="form-control" id="email" name="email" value="<?php echo $emailaddr; ?>">
                      </div> 
                     
                      <div class="form-group">
                        <input id="option2" name="specialoffers" type="checkbox" <?php if ($specialoffers == 1) { echo 'checked'; } ?>>
                        <label class="checkbox" for="option2"> Receive Special Offers </label>
                        <input id="option3" name="dtnotification" type="checkbox" <?php if ($downtimenotification == 1) { echo 'checked'; } ?>>
                        <label class="checkbox" for="option3"> Receive Downtime Notifications </label>
                        <input id="option4" name="isbanned" type="checkbox" <?php if ($isbanned == 1) { echo 'checked'; } ?>>
                        <label class="checkbox" for="option4"> Is Banned </label>
                        <input id="option5" name="portfwoption" type="checkbox" <?php if ($portfwoption == 1) { echo 'checked'; } ?>>
                        <label class="checkbox" for="option5"> Can Portforward </label>
                        <input id="option6" name="isactive" type="checkbox" <?php if ($isactive == 1) { echo 'checked'; } ?>>
                        <label class="checkbox" for="option6"> Email Verified </label>
                      </div>                  
                      <button type="submit" name="updateUser" class="btn btn-primary">Save</button>
                    </form>   
                    
                Open Ports
                     <table id="datatable" class="table table-hover tickets">
                                <thead>
                                    <th> Owner </th> <th>Port</th> <th>Protocol</th> <th>Internal IP</th>  <th> Server</th>  <th>Date Opened </th> <th>Date Closed</th> <th>Status</th> </tr> 
                                </thead>
                                <tbody>
                                    
                            <?php
                                
                                    $getPorts = $odb -> prepare("SELECT * FROM `ports` where `owner` = :userid AND `status` = 1 ORDER BY `id` DESC");
                                    $getPorts -> execute(array(':userid' => $userid));
                                    while ($getInfo = $getPorts -> fetch(PDO::FETCH_ASSOC))
                                    {
                                        $id = $getInfo['id'];
                                        $port = $getInfo['port'];
                                        $portType = $getInfo['portType'];
                                        $userID = $getInfo['owner'];
                                        $ip = $getInfo['internal_ip'];
                                        $server_id = $getInfo['server_id'];
                                        $status = $getInfo['status'];
                                        $dateOpened = date("m-d-Y h:i:s A", $getInfo['dateOpened']);
                                        $closed = $getInfo['dateClosed'];

                                        if ($status = 0){
                                          $dateClosed = date("m-d-Y h:i:s A", $getInfo['closed']);
                                        }
                                        else{
                                            $dateClosed = "N/A";
                                        }

                                        switch($getInfo['status'])
                                        {
                                            case 1:
                                            $status = '<a class="btn btn-primary" href="ports.php?close='.$id.'">Close</a>';
                                            break;
                                            case 0:
                                            $status = 'Closed';
                                            break;
                                       }

                                     //get server
                                        $getServer = $odb -> prepare("SELECT `name` FROM `servers` WHERE id = :id LIMIT 1");
                                        $getServer -> execute(array(':id' => $server_id));
                                        $serverName = $getServer ->  fetchColumn ( 0 );
                                    //get userName
                                        $getUsername = $odb -> prepare("SELECT `username` FROM `accounts` WHERE id = :id LIMIT 1");
                                        $getUsername -> execute(array(':id' => $userID));
                                        $userName = $getUsername ->  fetchColumn ( 0 );

                                   
                                    echo '
                                    <tr>
                                    <td> <a href="users.php?id=' . $userID . '"> ' . $userName. '</a> </td>
                                    <td>'.$port.'</td>
                                    <td>'.$portType.'</td>
                                    <td>'.$ip.'</td>
                                     <td>'.$serverName.'</td>
                                     <td>'.$dateOpened.'</td>
                                     <td>'.$dateClosed.'</td>
                                     <td>'.$status.'</td>
                                    </tr>'; 
                                    }
                            ?>
                                    
                                </tbody>
                            </table>   

                Payments
                    <table class="table table-hover tickets">
                                <thead>
                                    <tr> <th>Date</th> <th> Transaction ID </th> <th> User </th> <th>Amount</th> <th>Status</th> </tr> 
                                </thead>
                                <tbody>
                                    
                        <?php
                                $GetPayments = $odb -> prepare("SELECT * FROM `payment_logs` WHERE `payer` = :payer ORDER BY `id` DESC");
                                $GetPayments -> execute(array(':payer' => $userid));
                                while ($getInfo = $GetPayments -> fetch(PDO::FETCH_ASSOC))
                                {
                                    $id = $getInfo['id'];
                                    $tid = $getInfo['tid'];
                                    $payerid = $getInfo['payer'];
                                    $method = $getInfo['processor'];
                                    $amount = $getInfo['amount'];
                                    $date = date("m-d-Y h:i:s A", $getInfo['date']);
                                    $GetUsername = $odb -> prepare("SELECT `username` FROM `accounts` WHERE `id` = :id LIMIT 1");
                                    $GetUsername -> execute(array(':id' => $payerid));
                                    $paymentowner = $GetUsername -> fetch(PDO::FETCH_ASSOC);
                                    $username = $paymentowner['username'];
                                    
                                    if (($getInfo['valid']==1))
                                    {
                                    $valid = '<input type="hidden" class="validId" value="'.$getInfo['id'].'"><span class="setValidLink">Valid</span>';
                                    }
                                    elseif ($getInfo['valid']==0)
                                    {
                                    $valid = '<input type="hidden" class="validId" value="'.$getInfo['id'].'"><span class="setValidLink">Invalid</span>';                                    
                                    }
                                    else
                                    {
                                        $valid = "Unknown";
                                    }

                                    echo '
                                    <tr>
                                        <td>'.$date.'</td>
                                        <td>'.$tid.'</td>
                                        <td>'.$username.' ('.$payerid.') </td>
                                        <td>'.$method.' / '.$amount.'</td>
                                        <td>'.$valid.'</td>
                                    </tr>';
                                }
                            ?>
                                    
                                </tbody>
                    </table>  
                    
                </div>  

                </div>
                </div>   

                
                </div>
                <?php } else { ?>

                        <div class="table-responsive">
                            <button style="float:right;" class="btn btn-medium btn-default" id="toggleClosed">Show Banned Users</button>

                            <table id="datatable" class="table table-hover tickets">
                                <thead>
                                    <tr> <th> ID </th> <th> Username</th> <th>Email</th>  <th> Status</th> <th>Manage</th> </tr> 
                                </thead>
                                <tbody>
                                    
                            <?php
                                
                                    $GetUsers = $odb -> prepare("SELECT * FROM `accounts` ORDER BY `id` DESC");
                                    $GetUsers -> execute();
                                    while ($getInfo = $GetUsers -> fetch(PDO::FETCH_ASSOC))
                                    {
                                        $id = $getInfo['id'];
                                        $username = $getInfo['username'];
                                        $email = $getInfo['email'];
                                        switch($getInfo['isbanned'])
                                        {
                                            case 1:
                                            $status = '<span class="uk-badge uk-badge-danger">Banned</span>';
                                            break;
                                            case 0:
                                            $status = '<span class="uk-badge uk-badge-success">Active</span>';
                                            break;
                                            }

                                    echo '
                                    <tr>
                                    <td>'.$id.'</td>
                                    <td>'.$username.'</td>
                                    <td>'.$email.'</td>
                                    <td>'.$status.'</td>
                                    <td><a class="btn btn-primary" href="users.php?id='.$id.'">View</a></td>
                                    </tr>'; 
                                    }
                            ?>
                                    
                                </tbody>
                            </table>  
                        </div>  <?php } ?>
            </div>
        </div>
      </div>
    </div><!-- /.container -->

    <?php include('../templates/footer.php'); ?>
    <?php include('../templates/scriptjs.php'); ?>
    
<!-- DataTables -->
        <link href="../assets/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/css/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/css/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/css/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/css/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/css/dataTables.colVis.css" rel="stylesheet" type="text/css" />
<!--jQuery-->
        <script src="../assets/js/jquery.dataTables.min.js"></script>
        <script src="../assets/js/dataTables.bootstrap.js"></script>
        <script src="../assets/js/dataTables.buttons.min.js"></script>
        <script src="../assets/js/buttons.bootstrap.min.js"></script>
        <script src="../assets/js/jszip.min.js"></script>
        <script src="../assets/js/pdfmake.min.js"></script>
        <script src="../assets/js/vfs_fonts.js"></script>
        <script src="../assets/js/buttons.html5.min.js"></script>
        <script src="../assets/js/buttons.print.min.js"></script>

        <script src="../assets/js/dataTables.fixedHeader.min.js"></script>
        <script src="../assets/js/dataTables.keyTable.min.js"></script>
        <script src="../assets/js/dataTables.responsive.min.js"></script>
        <script src="../assets/js/responsive.bootstrap.min.js"></script>
        <script src="../assets/js/dataTables.scroller.min.js"></script>
        <script src="../assets/js/dataTables.colVis.js"></script>

        <script type="text/javascript">
    $(document).ready(function(){
        window.ordersTable = $('#datatable').dataTable({
            "order": [[ 3, "Active" ]],
        });
        
        window.ordersTable.fnFilter('^((?!Banned).)*$', 3, true); 
        var hidingTimedOut = true;
        
        $('#toggleClosed').click(function() { 
            if(hidingTimedOut) {
                window.ordersTable.fnFilter('', 3, true); 
                hidingTimedOut = false;
                $('#toggleClosed').text('Hide Banned Users');
            } else {
                window.ordersTable.fnFilter('^((?!Banned).)*$', 3, true); 
                hidingTimedOut = true;
                $('#toggleClosed').text('Show Banned Users');
            }
           
        });
    })
</script>
</html>

