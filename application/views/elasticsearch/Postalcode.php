<?php
require_once(dirname(__FILE__) . '/../common/header.php');

$query = $_GET;
$query_result = http_build_query($query);
?>

<h2>Table Postalcode</h2>
<p>Generate by : <i><b><?= $generate_by; ?></b></i> (<?= $execute_time; ?>) | <a href="<?= base_url('elasticsearch/postalcode/reset?' . $query_result); ?>"><i>Reset</i></a></p>

<div class="col-md-6 mb-5">
    <form method="GET" action="<?= base_url("elasticsearch/postalcode/"); ?>">
        <div class="form-inline">
            <select name="type" class="form-control mr-2">
                <option value="" <?= ($type == "") ? "selected" : ""; ?>>All</option>
                <option value="postalcode_id" <?= ($type == "postalcode_id") ? "selected" : ""; ?>>postalcode_id</option>
                <option value="id_kecamatan" <?= ($type == "id_kecamatan") ? "selected" : ""; ?>>id_kecamatan</option>
                <option value="alias_kecamatan" <?= ($type == "alias_kecamatan") ? "selected" : ""; ?>>alias_kecamatan</option>
                <option value="kelurahan" <?= ($type == "kelurahan") ? "selected" : ""; ?>>kelurahan</option>
                <option value="alias_kelurahan" <?= ($type == "alias_kelurahan") ? "selected" : ""; ?>>alias_kelurahan</option>
                <option value="kodepos" <?= ($type == "kodepos") ? "selected" : ""; ?>>kodepos</option>
                <option value="lat" <?= ($type == "lat") ? "selected" : ""; ?>>lat</option>
                <option value="lon" <?= ($type == "lon") ? "selected" : ""; ?>>lon</option>
                <option value="shipper_area_id" <?= ($type == "shipper_area_id") ? "selected" : ""; ?>>shipper_area_id</option>
            </select>
            <input type="text" name="search" class="form-control mr-2" value="<?= isset($search) ? $search : ''; ?>" />
            <button class="btn btn-default">Search</button>
        </div>
    </form>
</div>
<div class="col-md-6 mb-5">
    <div class="form-inline text-right">
        <a href="<?= base_url('elasticsearch/postalcode/crud'); ?>" class="btn btn-primary">Tambah</a>
    </div>
</div>
<table class="table table-striped">
    <thead>
        <tr>
            <th>postalcode_id</th>
            <th>id_kecamatan</th>
            <th>alias_kecamatan</th>
            <th>kelurahan</th>
            <th>alias_kelurahan</th>
            <th>kodepos</th>
            <th>lat</th>
            <th>lon</th>
            <th>shipper_area_id</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if (isset($data['data']) && count($data['data']) > 0) {
            foreach ($data['data'] as $row) {
        ?>
                <tr>
                    <td class="text-center"><?= $row['_source']['postalcode_id']; ?></td>
                    <td class="text-center"><?= $row['_source']['id_kecamatan']; ?></td>
                    <td class="text-center"><?= $row['_source']['alias_kecamatan']; ?></td>
                    <td><?= $row['_source']['kelurahan']; ?></td>
                    <td class="text-center"><?= $row['_source']['alias_kelurahan']; ?></td>
                    <td class="text-center"><?= $row['_source']['kodepos']; ?></td>
                    <td><?= $row['_source']['lat']; ?></td>
                    <td><?= $row['_source']['lon']; ?></td>
                    <td class="text-center"><?= $row['_source']['shipper_area_id']; ?></td>
                    <td class="text-center">
                        <a href="<?= base_url('elasticsearch/postalcode/crud/' . $row['_source']['postalcode_id']); ?>" class="btn btn-warning">Edit</a>
                        <a href="<?= base_url('elasticsearch/postalcode/delete_postalcode/' . $row['_source']['postalcode_id']); ?>" class="btn btn-danger">Delete</a>
                    </td>
                </tr>
        <?php
            }
        }
        ?>
    </tbody>
</table>
<ul class="pagination pagination-sm">
    <?php
    $query = $_GET;
    for ($i = 0; $i < $total_page; $i++) {
        $query['page'] = $i + 1;
        $query_result = http_build_query($query);
    ?>
        <li class="page-item <?= (($i + 1) == $cur_page) ? "active" : ""; ?>"><a class="page-link" href="<?= base_url('elasticsearch/postalcode/?' . $query_result); ?>"><?= $i + 1; ?></a></li>
    <?php
    }
    ?>
</ul>

<p>Total Data: <?= (isset($total_data)) ? $total_data : 0; ?> | Total Page: <?= (isset($total_page)) ? $total_page : 0; ?></p>
<?php require_once(dirname(__FILE__) . '/../common/footer.php'); ?>