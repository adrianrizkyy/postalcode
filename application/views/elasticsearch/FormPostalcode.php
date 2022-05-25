<?php require_once(dirname(__FILE__) . '/../common/header.php'); ?>

<h2><?= (isset($title)) ? $title : 'Postalcode'; ?></h2>

<?php if (isset($_SESSION['valid']['error'])) { ?>
    <div class="alert alert-warning alert-dismissible">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <strong>Warning!</strong> <?= implode(' , ', $_SESSION['valid']['error']) ?>
    </div>
<?php } ?>

<div class="panel panel-default">
    <div class="panel-heading"><?= (isset($title)) ? $title : 'Postalcode'; ?></div>
    <div class="panel-body">
        <form action="<?= $action; ?>" method="POST">

            <div class="form-group">
                <label for="postalcode_id">postalcode_id</label>
                <input type="text" name="postalcode_id" class="form-control" value="<?= (isset($_SESSION['valid']['data']['postalcode_id'])) ? $_SESSION['valid']['data']['postalcode_id'] : ""; ?>" readonly required />
            </div>

            <div class="form-group">
                <label for="id_kecamatan">id_kecamatan</label>
                <input type="text" name="id_kecamatan" class="form-control" value="<?= (isset($_SESSION['valid']['data']['id_kecamatan'])) ? $_SESSION['valid']['data']['id_kecamatan'] : ""; ?>" required />
            </div>
            <div class="form-group">
                <label for="alias_kecamatan">alias_kecamatan</label>
                <input type="text" name="alias_kecamatan" class="form-control" value="<?= (isset($_SESSION['valid']['data']['alias_kecamatan'])) ? $_SESSION['valid']['data']['alias_kecamatan'] : ""; ?>" required />
            </div>
            <div class="form-group">
                <label for="kelurahan">kelurahan</label>
                <input type="text" name="kelurahan" class="form-control" value="<?= (isset($_SESSION['valid']['data']['kelurahan'])) ? $_SESSION['valid']['data']['kelurahan'] : ""; ?>" required />
            </div>
            <div class="form-group">
                <label for="alias_kelurahan">alias_kelurahan</label>
                <input type="text" name="alias_kelurahan" class="form-control" value="<?= (isset($_SESSION['valid']['data']['alias_kelurahan'])) ? $_SESSION['valid']['data']['alias_kelurahan'] : ""; ?>" required />
            </div>
            <div class="form-group">
                <label for="kodepos">kodepos</label>
                <input type="text" name="kodepos" class="form-control" value="<?= (isset($_SESSION['valid']['data']['kodepos'])) ? $_SESSION['valid']['data']['kodepos'] : ""; ?>" required />
            </div>
            <div class="form-group">
                <label for="lat">lat</label>
                <input type="text" name="lat" class="form-control" value="<?= (isset($_SESSION['valid']['data']['lat'])) ? $_SESSION['valid']['data']['lat'] : ""; ?>" required />
            </div>
            <div class="form-group">
                <label for="lon">lon</label>
                <input type="text" name="lon" class="form-control" value="<?= (isset($_SESSION['valid']['data']['lon'])) ? $_SESSION['valid']['data']['lon'] : ""; ?>" required />
            </div>
            <div class="form-group">
                <label for="shipper_area_id">shipper_area_id</label>
                <input type="text" name="shipper_area_id" class="form-control" value="<?= (isset($_SESSION['valid']['data']['shipper_area_id'])) ? $_SESSION['valid']['data']['shipper_area_id'] : ""; ?>" required />
            </div>
            <div class="form-group text-right">
                <button class="btn btn-<?= $color; ?>" style="float:left;"><?= $button; ?></button>
                <a href="<?= base_url("elasticsearch/postalcode"); ?>" class="btn btn-default">Back</a>
            </div>
        </form>
    </div>
</div>

<?php require_once(dirname(__FILE__) . '/../common/footer.php'); ?>