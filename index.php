<?php
include './imports.php';

$login = '';
$password = '';
$regEmail = '';
$regPassword = '';
$regNickname = '';
$feedbackRegData = [];

$showFailedAuthMessage = false;

if ($_POST['loginButton']) {
    $login = $_POST['login'] ?: '';
    $password = $_POST['password'] ?: '';

    if ($users->logIn($login, $password)) {
        // Successful authorization. Let's go to phones page )
        header("Location: contacts.php");
        die();
    } else {
        $showFailedAuthMessage = true;
    }
}

if ($_POST['regButton']) {
    $regEmail = $_POST['regEmail'] ?: '';
    $regPassword = $_POST['regPassword'] ?: '';
    $regNickname = $_POST['regNickname'] ?: '';

    if ($users->add($regEmail, $regPassword, $regNickname)) {
        //Preventing re-registration by pressing F5 (sending post request again and again)
?>
        <div style="position: absolute; z-index:2000; width: 100%; height: 100%; background: white;
                    display: flex; justify-content: center; align-items: center; flex-direction: column">
            <h1>
                SUCCESS
            </h1>

            <br>
            <div class="d-flex justify-content-center">
                <div class="spinner-border" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
            <br>
            You will be redirected to authorization page in 3 sec...
        </div>
<?php
        header("Refresh:3; url=index.php");
    } else {
        $feedbackRegData = $users->errorList($regEmail, $regPassword, $regNickname);
    }
}
?>

<?php include './templates/header.php'; ?>

<?php if ($showFailedAuthMessage) { ?>
    <div class="alert alert-danger" role="alert">
        Nope! Wrong email or password.. or both
    </div>
<?php } ?>

<?php if($feedbackRegData) { ?>
    <div class="alert alert-danger" role="alert">
        <?php foreach ($feedbackRegData as $errMessage) { ?>
            <?= $errMessage.'<br>' ?>
        <?php } ?>
    </div>
<?php } ?>

<div class="d-flex justify-content-center vh-100">
    <div class="align-self-center">
        <div class="container p-3 bg-white rounded border border-primary">
            <div class="row">
                <div class="col-12">
                    <form action="index.php" method="post">
                        <div class="form-group">
                            <input
                                type="email"
                                name="login"
                                class="form-control"
                                id="exampleInputEmail1"
                                aria-describedby="emailHelp"
                                placeholder="Enter email"
                                value="<?= $login ?>"
                            >
                        </div>
                        <div class="form-group">
                            <input
                                type="password"
                                name="password"
                                class="form-control"
                                id="exampleInputPassword1"
                                placeholder="Password"
                                value="<?= $password ?>"
                            >
                        </div>
                        <button type="submit" class="btn btn-primary btn-block"
                                name="loginButton" value="sent"
                                style="border-radius: 5px 5px 0px 0px">
                            Log in
                        </button>

                        <hr>

                        <button type="button" class="btn btn-primary btn-block" data-toggle="modal"
                                data-target="#exampleModal" style="border-radius: 0px 0px 5px 5px">
                            Registration
                        </button>

                        <hr>

                        <a href="http://pt-10.khomyart.com/joinContactList.php">
                            <button type="button" class="btn btn-primary btn-block">
                                Contact list
                            </button>
                        </a>
                    </form>
                    <div class="modal fade"
                         id="exampleModal"
                         tabindex="-1"
                         role="dialog"
                         aria-labelledby="exampleModalLabel"
                         aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Registration</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form action="index.php" method="post">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Email address</label>
                                            <input type="email"
                                                   class="form-control"
                                                   id="exampleInputEmail1"
                                                   aria-describedby="emailHelp"
                                                   name="regEmail"
                                                   value="<?= $regEmail ?>"
                                            >
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputPassword1">Password</label>
                                            <input type="password"
                                                   class="form-control"
                                                   id="exampleInputPassword1"
                                                   name="regPassword"
                                                   value="<?= $regPassword ?>"
                                            >
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputNickname1">Nickname</label>
                                            <input type="text"
                                                   class="form-control"
                                                   id="exampleInputNickname1"
                                                   name="regNickname"
                                                   value="<?= $regNickname ?>"
                                            >
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit"
                                                    class="btn btn-primary"
                                                    name="regButton"
                                                    value="sent"
                                            >
                                                Confirm
                                            </button>
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                Close
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include './templates/footer.php'; ?>

