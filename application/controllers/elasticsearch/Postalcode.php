<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once(dirname(__FILE__) . "\../MasterController.php");

class Postalcode extends MasterController
{

    function __construct()
    {
        parent::__construct();

        $this->load->library('elasticsearch');
        $this->load->model('elasticsearch/PostalcodeModel', 'postalcode');

        $this->source = array('id', 'postalcode_id', 'id_kecamatan', 'alias_kecamatan', 'kelurahan', 'alias_kelurahan', 'kodepos', 'lat', 'lon', 'shipper_area_id');
        $this->key    = "postalcode";
        $this->limit  = 5;
        $this->sort  = ['_id' => 'desc'];
    }

    public function index()
    {
        $time_start     = microtime(true);

        $cur_page       = ($this->input->get('page')) ? $this->input->get('page') : 1;
        $type           = $this->input->get('type');
        $search         = $this->input->get('search');
        $key            = $this->key . "_" . $cur_page;
        $condition      = '';
        
        if ($type != "" && $search != "") {
            $condition = [
                'match' => [
                    $type => $search
                ]
            ];

            $key    = $key . "_" . $type . "_" . preg_replace('/[ ,]+/', '_', trim($search));
        }

        $data                   = array();
        $data['generate_by']    = "Memcache ($key)";
        $data['title']          = "View Postalcode";

        // Lets try to get the key
        $results    = $this->memcached_library->get($key);
        $total_data = $this->memcached_library->get('total_'.$key);

        // If the key does not exist it could mean the key was never set or expired
        if (!$results) {
            // $data['generate_by'] = "Query";

            // Modify this Query to your liking!
            // $query      = $this->postalcode->get_postalcode();
            // $results    = $query['data'];


            $data['generate_by'] = "Query by Elasticsearch";

            $source     = $this->source;
            $sort       = $this->sort;
            $skip       = ($cur_page - 1) * $this->limit;
            $results    = $this->elasticsearch->find("postalcode", $condition, $source, $sort, $skip);
            $total_data = $results['total_row'];

            // Lets store the results
            $this->memcached_library->set($key, $results);
            $this->memcached_library->set('total_'.$key, $total_data);
        }

        $total_page               = ($total_data > 0) ? ceil($total_data / $this->limit) : 0;
        $data['data']             = $results;
        $data['type']             = $type;
        $data['search']           = $search;
        $data['total_data']       = $total_data;
        $data['total_page']       = $total_page;
        $data['cur_page']         = $cur_page;
        $data['execute_time']     = (microtime(true) - $time_start);

        $this->load->view('elasticsearch/postalcode', $data);
    }

    public function reset()
    {
        $cur_page   = ($this->input->get('page')) ? $this->input->get('page') : 1;
        $type       = $this->input->get('type');
        $search     = $this->input->get('search');
        $key        = $this->key . "_" . $cur_page;

        if ($type != "") {
            $key    = $key . "_" . $type . "_" . preg_replace('/[ ,]+/', '_', trim($search));
        }

        $this->memcached_library->delete($key);

        redirect('/elasticsearch/postalcode');
    }

    public function reset_test()
    {
        for ($i = 1; $i <= 5; $i++) {
            $key        = $this->key . "_" . $i;
            $this->memcached_library->delete($key);
        }
    }

    public function crud($postalcode_id = '')
    {
        $data                   = array();
        $data['title']          = "Form Postalcode";
        $data['button']         = "Save";
        $data['color']          = "primary";
        $data['action']         = base_url("elasticsearch/postalcode/add_postalcode");

        $_SESSION["valid"]['data']['postalcode_id']  = $this->postalcode->get_postalcode_id();

        if ($postalcode_id != 0 && is_numeric($postalcode_id)) {
            if ($this->postalcode->check_postalcode_id($postalcode_id)) {
                $data['button']             = "Update";
                $data['color']              = "warning";
                $data['action']             = base_url("elasticsearch/postalcode/update_postalcode");
                $result                     = $this->postalcode->get_one_postalcode($postalcode_id);
                $_SESSION['valid']['data']  = $result['data'];
            }
        }

        $this->load->view('elasticsearch/formpostalcode', $data);

        unset($_SESSION["valid"]);
    }

    function add_postalcode()
    {
        $valid  = $this->form_validation();

        if (!$valid['status']) {
            $_SESSION['valid'] = $valid;

            redirect('/elasticsearch/postalcode/crud');
        }

        $result = $this->postalcode->add_postalcode($valid['data']);

        if ($result['status']) {
            $valid['data']['id']            = (int)$result['id'];
            $valid['data']['postalcode_id'] = (int)$result['id'];

            $this->elasticsearch->insert('postalcode', $valid['data']);
            $this->reset_test();

            redirect("/elasticsearch/postalcode");
        } else {
            echo "Terjadi Kesalahan! </br>" . $result['sql'];
        }
    }

    function update_postalcode()
    {
        $valid  = $this->form_validation();

        if (!$valid['status']) {
            $_SESSION['valid'] = $valid;

            redirect('/elasticsearch/postalcode/crud/' . $valid['data']['postalcode_id']);
        }

        $result = $this->postalcode->update_postalcode($valid['data']);

        if ($result['status']) {
            $valid['data']['id']            = $valid['data']['postalcode_id'];
            $valid['data']['postalcode_id'] = $valid['data']['postalcode_id'];

            // file get content error
            // $this->elasticsearch->updateOne('postalcode', $valid['data']['id'], $valid['data']);
            $this->elasticsearch->insert('postalcode', $valid['data']);
            $this->reset_test();

            redirect("/elasticsearch/postalcode");
        } else {
            echo "Terjadi Kesalahan! </br>" . $result['sql'];
        }
    }

    function form_validation()
    {
        $result                               = array();
        $result['message']                    = false;
        $result['status']                     = true;
        $result['data']['postalcode_id']      = $this->input->post('postalcode_id');
        $result['data']['id_kecamatan']       = $this->input->post('id_kecamatan');
        $result['data']['alias_kecamatan']    = $this->input->post('alias_kecamatan');
        $result['data']['kelurahan']          = $this->input->post('kelurahan');
        $result['data']['alias_kelurahan']    = $this->input->post('alias_kelurahan');
        $result['data']['kodepos']            = $this->input->post('kodepos');
        $result['data']['lat']                = $this->input->post('lat');
        $result['data']['lon']                = $this->input->post('lon');
        $result['data']['shipper_area_id']    = $this->input->post('shipper_area_id');

        if ($result['data']['postalcode_id'] == "") {
            $result['message'][]    = "postalcode_id tidak boleh kosong";
            $result['status']       = false;
        }

        if ($result['data']['id_kecamatan'] == "") {
            $result['message'][]    = "id_kecamatan tidak boleh kosong";
            $result['status']       = false;
        }

        if ($result['data']['alias_kecamatan'] == "") {
            $result['message'][]    = "alias_kecamatan tidak boleh kosong";
            $result['status']       = false;
        }

        if ($result['data']['kelurahan'] == "") {
            $result['message'][]    = "kelurahan tidak boleh kosong";
            $result['status']       = false;
        }

        if ($result['data']['alias_kelurahan'] == "") {
            $result['message'][]    = "alias_kelurahan tidak boleh kosong";
            $result['status']       = false;
        }

        if ($result['data']['kodepos'] == "") {
            $result['message'][]    = "kodepos tidak boleh kosong";
            $result['status']       = false;
        }

        if ($result['data']['lat'] == "") {
            $result['message'][]    = "lat tidak boleh kosong";
            $result['status']       = false;
        }

        if ($result['data']['lon'] == "") {
            $result['message'][]    = "lon tidak boleh kosong";
            $result['status']       = false;
        }

        if ($result['data']['shipper_area_id'] == "") {
            $result['message'][]    = "shipper_area_id tidak boleh kosong";
            $result['status']       = false;
        }

        return $result;
    }

    function delete_postalcode($postalcode_id)
    {
        $result = $this->postalcode->check_postalcode_id($postalcode_id);

        if ($result) {
            $response = $this->postalcode->delete_postalcode($postalcode_id);


            if ($response['status']) {
                $this->elasticsearch->deleteOne('postalcode', $postalcode_id);
                $this->reset_test();


                // var_dump($this->elasticsearch->deleteOne('postalcode', $postalcode_id));
                redirect("/elasticsearch/postalcode");
            } else {
                echo $response['message'];
            }
        } else {
            echo "Postalcode Id : $postalcode_id Tidak terdaftar!";
        }
    }

    public function test()
    {
        $table = [
            'id'                      => 'integer',
            'postalcode_id'           => 'integer',
            'id_kecamatan'            => 'integer',
            'alias_kecamatan'         => 'text',
            'kelurahan'               => 'text',
            'alias_kelurahan'         => 'text',
            'kodepos'                 => 'text',
            'lat'                     => 'text',
            'lon'                     => 'text',
            'shipper_area_id'         => 'integer'
        ];



        // var_dump($this->elasticsearch->create('postalcode', $table));

        $condition = [
            'match' => [
                'postalcode_id' => '2'
            ]
        ];

        $condition = '';

        $source = array('id', 'postalcode_id', 'id_kecamatan', 'alias_kecamatan', 'kelurahan', 'alias_kelurahan', 'kodepos', 'lat', 'lon', 'shipper_area_id');

        $sort   = array(
            "_id" => "desc"
        );

        echo "<pre>";
        print_r($this->elasticsearch->find("postalcode", $condition, $source, $sort, -1, 20));
        echo "</pre>";

        echo "<pre>";
        // print_r($this->elasticsearch->findOne("postalcode"));
        echo "</pre>";

        // var_dump($this->elasticsearch->deleteOne('postalcode', 10));


    }
}
