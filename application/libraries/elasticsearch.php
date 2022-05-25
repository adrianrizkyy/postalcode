<?php
class ElasticSearch
{
	public $ci;
	public $server;
	public $auth = false;
	public $username;
	public $password;
	// public $method = "file_get_contents";
	public $method = "curl";

	public function __construct()
	{
		$this->ci = &get_instance();
		$this->ci->config->load("elasticsearch");
		$this->server = $this->ci->config->item('es_server');
		$this->auth = $this->ci->config->item('auth');
		$this->username = $this->ci->config->item('username');
		$this->password = $this->ci->config->item('password');
	}

	private function call($method = 'GET', $index = "", $path = "", $data = null, $id = '')
	{
		if (!empty($index)) {
			$url = $this->server . '/' . $index;
		} else {
			$url = $this->server;
		}
		if (!empty($path)) $url .= '/' . $path;
		if (!empty($id)) $url .= '/' . $id;

		if ($this->method == "file_get_contents") {
			if (is_array($data)) $data = json_encode($data);

			if ($method == 'GET') {
				if ($data === null || empty($data)) {
					if ($this->auth) {
						$opts = array(
							'http' =>
							array(
								'method'  => $method,
								'header'  => "Authorization: Basic " . base64_encode("$this->username:$this->password") . "\r\n" .
									"Accept: application/json\r\n" .
									"Content-Type: application/json",
							)
						);
					} else {
						$opts = array(
							'http' =>
							array(
								'method'  => $method,
								'header'  => "Content-Type: application/json\r\n" .
									"Accept: application/json"
							)
						);
					}
				} else {
					if ($this->auth) {
						$opts = array(
							'http' =>
							array(
								'method'  => $method,
								'header'  => "Authorization: Basic " . base64_encode("$this->username:$this->password") . "\r\n" .
									"Accept: application/json\r\n" .
									"Content-Type: application/json",
								'content' => $data
							)
						);
					} else {
						$opts = array(
							'http' =>
							array(
								'method'  => $method,
								'header'  => "Content-Type: application/json\r\n" .
									"Accept: application/json",
								'content' => $data
							)
						);
					}
				}
			} else {
				if ($this->auth) {
					$opts = array(
						'http' =>
						array(
							'method'  => $method,
							'header'  => "Authorization: Basic " . base64_encode("$this->username:$this->password") . "\r\n" .
								"Accept: application/json\r\n" .
								"Content-Type: application/json",
							'content' => $data
						)
					);
				} else {
					$opts = array(
						'http' =>
						array(
							'method'  => $method,
							'header'  => "Content-Type: application/json\r\n" .
								"Accept: application/json",
							'content' => $data
						)
					);
				}
			}

			$context = stream_context_create($opts);
			$response = @file_get_contents($url, false, $context);
			$http_code = "";
			$http_message = "";
			$status_response_header = isset($http_response_header[0]) ? $http_response_header[0] : "";
			
			$error = isset($response['error']['reason']) ? $response['error']['reason'] : "";

			if (!empty($status_response_header)) {
				preg_match('{HTTP\/\S*\s(\d{3})}', $status_response_header, $match);
				$http_code = isset($match[1]) ? $match[1] : "";
				$arrMessage = explode($http_code, $status_response_header);
				$http_message = isset($arrMessage[1]) ? trim($arrMessage[1]) : "";
			}

			// echo $http_code."<br>";
			// echo $url."<br>";
			// echo "<pre>";
			// print_r($opts);
			// print_r($response);
			// echo "</pre>";

			if ($response === FALSE) {
				$respond = array();
				$respond['status'] = 0;
				if ($http_code !== 200) {
					$respond['error']['type'] = $http_code;
					$respond['error']['reason'] = $http_message;
				} else {
					$respond['error']['type'] = $error;
					$respond['error']['reason'] = $error;
				}
				return $respond;
			} else {
				return json_decode($response, true);
			}
		} else {
			if ($this->auth) {
				$headers = array(
					'Accept: application/json',
					'Content-Type: application/json',
					'Authorization: Basic ' . base64_encode("$this->username:$this->password") . ''
				);
			} else {
				$headers = array(
					'Accept: application/json',
					'Content-Type: application/json'
				);
			}

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			if ($this->auth) {
				curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
				curl_setopt($ch, CURLOPT_USERPWD, $this->username . ':' . $this->password);
			}

			switch ($method) {
				case 'GET':
					if ($data !== null || !empty($data)) {
						curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
					}
					break;
				case 'POST':
					curl_setopt($ch, CURLOPT_POST, true);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
					break;
				case 'PUT':
					curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
					curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
					break;
				case 'DELETE':
					curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
					break;
			}

			$response = curl_exec($ch);
			$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);

			if ($http_code !== 200) {
				$response = json_encode(array("status" => 0, "error" => array("type" => $http_code, "reason" => $response)));
			}

			// echo $url."<br>";
			// echo $http_code."<br>";
			// echo "<pre>";
			// print_r($response);
			// echo "</pre>"; 

			return json_decode($response, true);
		}
	}

	public function infoStatus()
	{
		$result = array();

		$response1 = $this->call('GET', '', '_cluster/health');
		$result['status'] = $response1['status'];
		$result['error_reason'] = isset($response1['error']['reason']) ? $response1['error']['reason'] : "";
		$result['cluster_name'] = $response1['cluster_name'];

		$response2 = $this->call('GET', '');
		$result['version_number'] = $response2['version']['number'];
		$result['version'] = intval($response2['version']['number']);

		$response3 = $this->call('GET', '', '_cluster/state');
		$master_node = isset($response3['master_node']) ? $response3['master_node'] : "";
		$result['master_node'] = $master_node;

		if (!empty($master_node)) {
			/* $url = $this->server.'/_cat/allocation';
			$response4 = @file_get_contents($url);
			
			
			if($response !== FALSE){
				$arrResponse4 = explode(" ",$response4);
				
				if(count($arrResponse4) > 0){
					$result['disk_indices'] = isset($arrResponse4[1])?$arrResponse4[1]:0;
					$result['disk_used'] = isset($arrResponse4[2])?$arrResponse4[2]:0;
					$result['disk_available'] = isset($arrResponse4[3])?$arrResponse4[3]:0;
					$result['disk_total'] = isset($arrResponse4[4])?$arrResponse4[4]:0;
					$result['disk_percent'] = isset($arrResponse4[5])?$arrResponse4[5]:0;
				}
			} */

			$response5 = $this->call('GET', '', '_nodes/stats/fs');
			$result['nodes'] = $response5['_nodes'];

			/* $nodes_detail = array();
			foreach($response5['nodes'] as $idx => $nodes){
				$nodes_detail[$idx]['fs']['total'] = $nodes['fs']['total'];
			}	
			$result['nodes_detail'] = $nodes_detail; */
		}

		return $result;
	}

	public function create($index, $tables = array())
	{
		$result = array();
		$result['status'] = 0;

		if (count($tables) > 0) {
			$param_property = array();
			foreach ($tables as $field => $type) {
				if ($type == "date") {
					$param_property[$field] = array("type" => "date", "format" => "yyyy-MM-dd HH:mm:ss");
				} else if ($type == "text") {
					$param_property[$field] = array("type" => "text", "analyzer" => "custom_analyzer", "fielddata" =>  true);
				} else if ($type == "integer") {
					$param_property[$field] = array("type" => "integer");
				} else if ($type == "geo") {
					$param_property[$field] = array("type" => "geo_point");
				} else if ($type == "keyword") {
					$param_property[$field] = array("type" => "keyword");
				} else {
					$param_property[$field] = array("type" => "double");
				}

				$property = json_encode($param_property, JSON_PRETTY_PRINT);
			}

			//build mapping
			$req = '{
				 "settings": {
					"analysis": {
					   "analyzer": {
						  "custom_analyzer": {
							 "type": "custom",
							 "tokenizer": "standard",
							 "filter": [
								"lowercase"
							 ]
						  }
					   }
					}
				 },
				 "mappings": {
				   "properties": ' . $property . '
				 }
				}';

			$data = json_decode($req, true);
			$response = $this->call('PUT', $index, '', $data);

			$acknowledged = isset($response['acknowledged']) ? $response['acknowledged'] : false;
			$error = isset($response['error']['reason']) ? $response['error']['reason'] : "";

			if ($acknowledged == true) {
				$result['status'] = 1;
				$result['index'] = $response['index'];
			} else {
				$result['error_reason'] = $error;
			}
		}

		return $result;
	}


	public function find($index, $condition = array(), $fields = array(), $sort = array(), $from = -1, $limit = 5)
	{
		$options = array();
		if (!empty($fields)) {
			$options['_source'] = $fields;
		} else {
			$options['_source'] = false;
		}
		if (!empty($condition)) {
			$options['query'] = $condition;
		}
		if (!empty($sort)) {
			$options['sort'] = $sort;
		}
		if ($from >= 0) {
			$options['from'] = $from;
		}
		if ($limit > 0) {
			$options['size'] = $limit;
		}

		$response = $this->call('GET', $index, '_search', $options);

		$error = isset($response['error']['reason']) ? $response['error']['reason'] : "";

		$result = array();
		$result['status'] = 0;
		if (!empty($error)) {
			$result['error_reason'] = $error;
		} else {
			$total_row = isset($response['hits']['total']['value']) ? $response['hits']['total']['value'] : 0;

			if ($total_row > 0) {
				$data = isset($response['hits']['hits']) ? $response['hits']['hits'] : array();

				$result['status'] = 1;
				$result['total_row'] = $total_row;
				$result['data'] = $data;
			} else {
				$result['error_reason'] = "record not exist";
			}
		}

		return $result;
	}


	public function findOne($index, $condition = array(), $fields = array())
	{
		$options = array();
		if (!empty($fields)) {
			$options['_source'] = $fields;
		}
		if (!empty($condition)) {
			$options['query'] = $condition;
		}
		$options['size'] = 1;

		$response = $this->call('GET', $index, '_search', $options);

		$error = isset($response['error']['reason']) ? $response['error']['reason'] : "";

		$result = array();
		$result['status'] = 0;
		if (!empty($error)) {
			$result['error_reason'] = $error;
		} else {
			$total_row = isset($response['hits']['total']['value']) ? $response['hits']['total']['value'] : 0;

			if ($total_row > 0) {
				$data = isset($response['hits']['hits']) ? $response['hits']['hits'][0] : array();

				$result['status'] = 1;
				$result['total_row'] = $total_row;
				$result['data'] = $data;
			} else {
				$result['error_reason'] = "record not exist";
			}
		}

		return $result;
	}


	public function insert($index, $data = array())
	{
		$result = array();
		$result['status'] = 0;

		if (count($data) > 0) {
			$id = isset($data['id']) ? $data['id'] : "";

			$response = $this->call('PUT', $index, '_doc', $data, $id);

			$result_status = isset($response['result']) ? $response['result'] : "";
			$error = isset($response['error']['reason']) ? $response['error']['reason'] : "";

			if (empty($error)) {
				$result['status'] = 1;
				$result['result'] = $result_status;
			} else {
				$result['error_reason'] = $error;
			}
		}

		return $result;
	}


	public function updateIncrement($index, $id, $field, $data = array())
	{
		$result = array();
		$result['status'] = 0;

		$request = array(
			'script' => "ctx._source." . $field . "+=1",
			'upsert' => $data
		);

		$response = $this->call('POST', $index, '_update', $request, $id);

		$result_status = isset($response['result']) ? $response['result'] : "";
		$successful = isset($response['_shards']['successful']) ? $response['_shards']['successful'] : 0;
		$error = isset($response['error']['reason']) ? $response['error']['reason'] : "";

		if ($successful) {
			$result['status'] = 1;
			$result['result'] = $result_status;
		} else {
			$result['error_reason'] = $error;
		}

		return $result;
	}


	public function updateOne($index, $id, $data = array(), $upsert = false)
	{
		$result = array();
		$result['status'] = 0;

		$request = array(
			'doc' => $data
		);

		if ($upsert) {
			$request['doc_as_upsert'] = true;
		}

		$response = $this->call('POST', $index, '_update', $request, $id);

		$result_status = isset($response['result']) ? $response['result'] : "";
		$successful = isset($response['_shards']['successful']) ? $response['_shards']['successful'] : 0;
		$error = isset($response['error']['reason']) ? $response['error']['reason'] : "";

		if ($successful) {
			$result['status'] = 1;
			$result['result'] = $result_status;
		} else {
			$result['error_reason'] = $error;
		}

		return $result;
	}


	public function updateMany($index, $condition = array(), $data = array())
	{
		$result = array();
		$result['status'] = 0;

		if (!empty($condition) && !empty($data)) {
			$options = array();
			//$options['query'] = array("term" => $condition);
			$options['query'] = $condition;

			$strData = "";
			foreach ($data as $field => $value) {
				if (gettype($value) == "integer") {
					$strData .= 'ctx._source.' . $field . '=' . $value . ';';
				} else {
					$strData .= 'ctx._source.' . $field . '="' . $value . '";';
				}
			}
			$options['script'] = array("source" => $strData);

			$response = $this->call('POST', $index, '_update_by_query?conflicts=proceed', $options);

			$updated = isset($response['updated']) ? $response['updated'] : 0;
			$total = isset($response['total']) ? $response['total'] : 0;
			$error = isset($response['error']['reason']) ? $response['error']['reason'] : "";

			if ($updated > 0) {
				$result['status'] = 1;
				$result['total'] = $total;
			} else {
				$result['error_reason'] = $error;
			}
		} else {
			$result['error_reason'] = "query is required";
		}

		return $result;
	}


	public function deleteOne($index, $id)
	{
		$result = array();
		$result['status'] = 0;


		$response = $this->call('DELETE', $index, '_doc',  array(), $id);
		$error = isset($response['error']['reason']) ? $response['error']['reason'] : "";

		if (empty($error)) {
			$result['status'] = 1;
		} else {
			$result['error_reason'] = $error;
		}

		return $result;
	}


	public function deleteMany($index, $condition = array())
	{
		$result = array();
		$result['status'] = 0;

		$options = array();
		$options['query'] = array("match_all" => new stdClass);
		if (!empty($condition)) {
			$options['query'] = $condition;
		}

		$response = $this->call('POST', $index, '_delete_by_query?conflicts=proceed', $options);

		$deleted = isset($response['deleted']) ? $response['deleted'] : 0;
		$error = isset($response['error']['reason']) ? $response['error']['reason'] : "";

		if ($deleted > 0) {
			$result['status'] = 1;
		} else {
			$result['error_reason'] = $error;
		}

		return $result;
	}


	public function remove($index)
	{
		$result = array();
		$result['status'] = 0;

		$response = $this->call('DELETE', $index);

		$acknowledged = isset($response['acknowledged']) ? $response['acknowledged'] : false;
		$error = isset($response['error']['reason']) ? $response['error']['reason'] : "";

		if ($acknowledged) {
			$result['status'] = 1;
		} else {
			$result['error_reason'] = $error;
		}

		return $result;
	}


	public function map($index, $data = array())
	{
		$response = $this->call('GET', $index, '_mapping', $data);

		$error = isset($response['error']['reason']) ? $response['error']['reason'] : "";

		$result = array();
		$result['status'] = 0;
		if (!empty($error)) {
			$result['error_reason'] = $error;
		} else {
			$result['status'] = 1;
			$result['data'] = $response;
		}

		return $result;
	}


	public function aggregations($index, $aggs = array(), $condition = array(), $size = 0)
	{
		$options = array();

		$options['size'] = $size;
		if (!empty($aggs)) {
			$options['aggs'] = $aggs;
		}
		$options['query'] = array("match_all" => new stdClass);
		if (!empty($condition)) {
			$options['query'] = $condition;
		}

		$response = $this->call('GET', $index, '_search', $options);

		$error = isset($response['error']['reason']) ? $response['error']['reason'] : "";

		$result = array();
		$result['status'] = 0;
		if (!empty($error)) {
			$result['error_reason'] = $error;
		} else {
			$data = isset($response['aggregations']) ? $response['aggregations'] : array();

			$result['status'] = 1;
			$result['data'] = $data;
		}

		return $result;
	}


	public function query_all($index, $query)
	{
		$response = $this->call('GET', $index, '_search?' . http_build_query(array('q' => $query)));

		$error = isset($response['error']['reason']) ? $response['error']['reason'] : "";

		$result = array();
		$result['status'] = 0;
		if (!empty($error)) {
			$result['error_reason'] = $error;
		} else {
			$total_row = isset($response['hits']['total']['value']) ? $response['hits']['total']['value'] : 0;

			if ($total_row > 0) {
				$data = isset($response['hits']['hits']) ? $response['hits']['hits'] : array();

				$result['status'] = 1;
				$result['total_row'] = $total_row;
				$result['data'] = $data;
			} else {
				$result['error_reason'] = "record not exist";
			}
		}

		return $result;
	}
}
