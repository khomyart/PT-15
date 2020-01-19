<?php
include './imports.php';

$searchValue = '';

if (isset($_REQUEST['isSearch']) && ($_REQUEST['isSearch'] == 'Y')) {
    $searchValue = $_REQUEST['search'];
}

$phones = $contacts->getJoinList($searchValue);
?>

<?php include './templates/header.php'; ?>

<div class="container">
    <div class="row">
        <div class="col">
            <br />
            <form class="form-inline d-flex justify-content-between" action="" method="get">
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
            </form>

            <br />

            <table class="table table-striped">
                <thead>
                <tr>
                    <th width="5%">Id</th>
                    <th width="20%">Phone</th>
                    <th width="15%">First name</th>
                    <th width="15%">Last name</th>
                    <th width="25%">Email</th>
                    <th width="20%">Nickname</th>
                </tr>
                </thead>
                <tbody>
                    <?php foreach ($phones as $phoneData) {?>
                        <tr>
                            <td><?= $phoneData['id'] ?></td>
                            <td><?= $phoneData['phone'] ?></td>
                            <td><?= $phoneData['first_name'] ?></td>
                            <td><?= $phoneData['last_name'] ?></td>
                            <td><?= $phoneData['email'] ?></td>
                            <td><?= $phoneData['nickname'] ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include './templates/footer.php'; ?>
