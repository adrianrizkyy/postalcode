<?php

class PostalcodeModel extends CI_Model
{

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    function get_postalcode_id()
    {
        $sql    = "SELECT (IFNULL(MAX(postalcode_id),0) + 1) as postalcode_id FROM `postalcode_es`";
        $query  = $this->db->query($sql);
        $row    = $query->row_array();

        return $row['postalcode_id'];
    }

    function get_total_postalcode($where = '')
    {
        $sql    = "SELECT COUNT(*) as total FROM `postalcode_es` $where";
        $query  = $this->db->query($sql);
        $row    = $query->row_array();

        return $row['total'];
    }

    function check_postalcode_id($postalcode_id)
    {
        $result = false;

        $sql    = "SELECT COUNT(*) as total FROM `postalcode_es` WHERE `postalcode_id` = '$postalcode_id'";
        $query  = $this->db->query($sql);

        if($query){
            $row    = $query->row_array();
            $result = $row['total'];
        }
        
        
        return $result;
    }

    function get_postalcode($where = '', $skip = '0', $limit = '5')
    {
        $result             = array();
        $result['data']     = false;
        $result['status']   = false;
        $result['message']  = "Terjadi Kesalahan dalam Execute Query";

        $sql   = "SELECT * FROM `postalcode_es` $where LIMIT $skip, $limit";
        $result['sql']      = $sql;
        $query = $this->db->query($sql);

        if ($query) {
            $result['status']   = true;
            $result['message']  = "Berhasil Execute Query";
            $result['data']     = $query->result_array();
        }

        return $result;
    }

    function get_one_postalcode($postalcode_id)
    {
        $result             = array();
        $result['data']     = false;
        $result['status']   = false;
        $result['message']  = "Terjadi Kesalahan dalam Execute Query";

        $sql   = "SELECT * FROM `postalcode_es` WHERE `postalcode_id` = $postalcode_id";
        $result['sql']      = $sql;
        $query = $this->db->query($sql);
        
        if ($query) {
            $result['status']   = true;
            $result['message']  = "Berhasil Execute Query";
            $result['data']     = $query->row_array();
        }

        return $result;
    }

    function add_postalcode($data)
    {
        $result             = array();
        $result['status']   = false;
        $result['message']  = "Terjadi Kesalahan dalam Execute Query";

        $sql    = "INSERT INTO `postalcode_es`(`id_kecamatan`, `alias_kecamatan`, `kelurahan`, `alias_kelurahan`, `kodepos`, `lat`, `lon`, `shipper_area_id`) VALUES ('" . $data['id_kecamatan'] . "', '" . $data['alias_kecamatan'] . "', '" . $data['kelurahan'] . "', '" . $data['alias_kelurahan'] . "', '" . $data['kodepos'] . "', '" . $data['lat'] . "', '" . $data['lon'] . "', '" . $data['shipper_area_id'] . "');";
        $result['sql']      = $sql;

        $query = $this->db->query($sql);

        if ($query) {
            $result['status']   = true;
            $result['message']  = "Berhasil Execute Query";
            $result['id']       = $this->db->insert_id();;
        }

        return $result;
    }

    function update_postalcode($data)
    {
        $result             = array();
        $result['status']   = false;
        $result['message']  = "Terjadi Kesalahan dalam Execute Query";

        $sql    = "UPDATE `postalcode_es` set `id_kecamatan` = '" . $data['id_kecamatan'] . "', `alias_kecamatan` = '" . $data['alias_kecamatan'] . "', `kelurahan` = '" . $data['kelurahan'] . "',`alias_kelurahan` = '" . $data['alias_kelurahan'] . "', `kodepos` = '" . $data['kodepos'] . "', `lat` = '" . $data['lat'] . "', `lon` = '" . $data['lon'] . "', `shipper_area_id` = '" . $data['shipper_area_id'] . "' WHERE `postalcode_id` = '".$data['postalcode_id']."'";
        $result['sql']      = $sql;

        $query = $this->db->query($sql);

        if ($query) {
            $result['status']   = true;
            $result['message']  = "Berhasil Execute Query";
            $result['id']       = $this->db->insert_id();;
        }

        return $result;
    }

    function delete_postalcode($postalcode_id)
    {
        $result             = array();
        $result['status']   = false;
        $result['message']  = "Terjadi Kesalahan dalam Execute Query";

        $sql                = "DELETE FROM `postalcode_es` WHERE `postalcode_id` = $postalcode_id";
        $result['sql']      = $sql;
        
        $query = $this->db->query($sql);

        if ($query) {
            $result['status']   = true;
            $result['message']  = "Berhasil Execute Query";
        }

        return $result;
    }

}
