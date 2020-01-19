<?php
include './imports.php';

$searchValue = '';
$phoneCodes=['+380', '+375', '+963', '+7'];
$phone = '';
$firstName = '';
$lastName = '';
$feedbackContactError = [];

if (isset($_REQUEST['isSearch']) && ($_REQUEST['isSearch'] == 'Y')) {
    $searchValue = $_REQUEST['search'];
}

$phones = $contacts->getList($searchValue);

if(isset($_POST['contactCreate'])) {
    $phone = $_POST['phoneNumberNoCode'] ? $_POST['phoneCode'].$_POST['phoneNumberNoCode'] : '';
    $firstName = $_POST['firstName'] ?: '';
    $lastName = $_POST['lastName'] ?: '';

    if($contacts->add($phone, $firstName, $lastName)) {
        header('Location: Contacts.php');
        die();
    } else {
        $feedbackContactError = $contacts->errorsList($phone, $firstName, $lastName);
    }
}

if(isset($_POST['contactEditSave'])) {
    $id = $_POST['contactEditSave'];
    $phone = $_POST['phoneEdit'];
    $firstName = $_POST['firstNameEdit'];
    $lastName = $_POST['lastNameEdit'];

    if($contacts->edit($id, $phone, $firstName, $lastName)) {
        header('Location: Contacts.php');
        die();
    } else {
        $feedbackContactError = $contacts->errorsList($phone, $firstName, $lastName);
    }
}

if(isset($_POST['contactRemove'])) {
    $id = $_POST['contactRemove'];

    if($contacts->remove($id)) {
        header('Location: Contacts.php');
        die();
    }
}
?>

<?php include './templates/header.php'; ?>
<?php include './templates/navigation.php'; ?>

<div class="container">
    <?php
    if($feedbackContactError) { ?>
        <div class="alert alert-danger" role="alert">
            <?php foreach ($feedbackContactError as $errMessage) { ?>
                <?= $errMessage.'<br>' ?>
            <?php } ?>
        </div>
    <?php } ?>

    <div class="row">
        <div class="col">
            <br />

            <form class="form-inline d-flex justify-content-between" action="contacts.php" method="get">
                <div>
                    <input type="hidden" name="isSearch" value="Y">
                    <div class="form-group">
                        <label for="search" class="sr-only">Text to search</label>
                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            id="search"
                            placeholder="Text to search"
                            value="<?= $searchValue ?>">
                        <button type="submit" class="btn btn-primary" style="margin-left: 5px;">Search</button>
                    </div>
                </div>
                <div>
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
                        Add contact
                    </button>
                </div>
            </form>
            <div class="modal fade"
                 id="exampleModal"
                 tabindex="-1"
                 role="dialog"
                 aria-labelledby="exampleModalLabel"
                 aria-hidden="true"
            >
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form action="" method="post">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">New contact</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Phone number</label>
                                    <div class="d-flex">
                                        <select class="form-control w-25" name="phoneCode">

                                            <?php
                                            foreach($phoneCodes as $code) {
                                                if ($code == $_POST['phoneCode']) {
                                            ?>
                                                    <option selected
                                                            value="<?= $code ?>">
                                                        <?= $code ?>
                                                    </option>
                                            <?php
                                                } else {
                                            ?>
                                                <option value="<?= $code ?>">
                                                    <?= $code ?>
                                                </option>
                                            <?php
                                                }
                                            }
                                            ?>

                                        </select>
                                        <div class="input-group w-75">
                                            <input type="number"
                                                   class="form-control"
                                                   name="phoneNumberNoCode"
                                                   value="<?= $_POST['phoneNumberNoCode'] ?>"
                                            >
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>First name</label>
                                    <input type="text" class="form-control"
                                           name="firstName"
                                           value="<?= $firstName ?>"
                                    >
                                </div>
                                <div class="form-group">
                                    <label>Last name</label>
                                    <input type="text"
                                           class="form-control"
                                           name="lastName"
                                           value="<?= $lastName ?>"
                                    >
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary" name="contactCreate" value="submit">
                                    Save changes
                                </button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                    Close
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <br />

            <table class="table table-striped">
                <thead>
                <tr>
                    <th width="20%">Phone</th>
                    <th width="38%">First name</th>
                    <th width="38%">Last name</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($phones as $phoneData) { ?>
                    <tr>
                        <form action="" method="post">
                            <?php if($_POST['contactEditModeEnable'] === $phoneData['id']) {?>
                                <td>
                                    <input class="w-100 text-center" name="phoneEdit"
                                           value="<?= $phoneData['phone'] ?>"
                                    >
                                </td>
                                <td>
                                    <input class="w-100 text-center" name="firstNameEdit"
                                           value="<?= $phoneData['first_name'] ?>"
                                    >
                                </td>
                                <td>
                                    <input class="w-100 text-center" name="lastNameEdit"
                                           value="<?= $phoneData['last_name'] ?>"
                                    >
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-secondary dropdown-toggle"
                                                type="button"
                                                id="dropdownMenuButton"
                                                data-toggle="dropdown"
                                                aria-haspopup="true"
                                                aria-expanded="false">
                                        </button>
                                        <div class="dropdown-menu" style="margin-left:-114px" aria-labelledby="dropdownMenuButton">
                                            <button class="btn btn-secondary dropdown-item"
                                                    type="submit"
                                                    name="contactEditSave"
                                                    value="<?= $phoneData['id'] ?>"
                                            >
                                                Save
                                            </button>
                                            <button class="btn btn-secondary dropdown-item"
                                                    type="submit"
                                                    name="contact_edit_cancel"
                                                    value="cancel"
                                            >
                                                Cancel
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            <?php } else {?>
                            <td><?= $phoneData['phone'] ?></td>
                            <td><?= $phoneData['first_name'] ?></td>
                            <td><?= $phoneData['last_name'] ?></td>
                            <td width="30">
                                <div class="dropdown">
                                    <button class="btn btn-secondary dropdown-toggle"
                                            type="button"
                                            id="dropdownMenuButton"
                                            data-toggle="dropdown"
                                            aria-haspopup="true"
                                            aria-expanded="false">
                                    </button>
                                    <div class="dropdown-menu" style="margin-left:-114px" aria-labelledby="dropdownMenuButton">
                                        <a href="tel:<?= $phoneData['phone'] ?>" class="dropdown-item">
                                            Make a call
                                        </a>
                                        <hr>
                                        <button class="dropdown-item"
                                                type="submit"
                                                name="contactEditModeEnable"
                                                value="<?= $phoneData['id'] ?>"
                                        >
                                            Edit
                                        </button>
                                        <button class="dropdown-item"
                                                type="submit"
                                                name="contactRemove"
                                                value="<?= $phoneData['id'] ?>"
                                        >
                                            Remove
                                        </button>
                                    </div>
                                </div>
                            </td>
                            <?php } ?>
                        </form>
                    </tr>
                <?php }  ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include './templates/footer.php'; ?>
